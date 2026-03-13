<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductSitePrice;
use App\Services\CartService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function handle(Customer $customer, string $cartToken, array $data): Order
    {
        $currentSite = current_site();

        $cart = $this->cartService->get($cartToken);
        $items = Arr::get($cart, 'items', []);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => ['Cart empty.'],
            ]);
        }

        $productSitePrices = ProductSitePrice::query()
            ->with('product')
            ->whereIn('product_id', array_keys($items))
            ->where('site_id', $currentSite->id)
            ->get()
            ->keyBy('product_id');

        $total = 0;
        $orderItems = [];
        $invalidProductIds = [];
        $outOfStockProductIds = [];

        foreach ($items as $productId => $item) {
            $productSitePrice = $productSitePrices->get($productId);

            if (!$productSitePrice) {
                $invalidProductIds[] = $productId;
                continue;
            }

            $quantity = Arr::get($item, 'quantity');

            if ($quantity > $productSitePrice->product->stock) {
                $outOfStockProductIds[] = $productId;
                continue;
            }

            $lineTotal = $productSitePrice->price * $quantity;
            $total += $lineTotal;

            $orderItems[] = [
                'product_id' => $productSitePrice->product_id,
                'product_name_snapshot' => $productSitePrice->product->name,
                'unit_price' => $productSitePrice->price,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        if (!empty($invalidProductIds)) {
            throw ValidationException::withMessages([
                'items' => ['Some products are unavailable for this site: ' . implode(', ', $invalidProductIds) . '.'],
            ]);
        }

        if (!empty($outOfStockProductIds)) {
            throw ValidationException::withMessages([
                'items' => ['Some products exceed available stock: ' . implode(', ', $outOfStockProductIds) . '.'],
            ]);
        }

        if (empty($orderItems)) {
            throw ValidationException::withMessages([
                'items' => ['Cart empty.'],
            ]);
        }

        $order = DB::transaction(function () use ($currentSite, $customer, $orderItems, $data, $total) {
            $order = $customer->orders()->create([
                'site_id' => $currentSite->id,
                'status' => OrderStatus::Pending,
                'payment_method' => Arr::get($data, 'payment_method'),
                'shipping_full_name' => Arr::get($data, 'shipping_full_name'),
                'shipping_full_address' => Arr::get($data, 'shipping_full_address'),
                'shipping_city' => Arr::get($data, 'shipping_city'),
                'shipping_country' => Arr::get($data, 'shipping_country'),
                'total' => $total,
                'paid_amount' => 0,
            ]);

            $order->items()->createMany($orderItems);

            return $order;
        });

        $this->cartService->clear($cartToken);

        return $order;
    }
}
