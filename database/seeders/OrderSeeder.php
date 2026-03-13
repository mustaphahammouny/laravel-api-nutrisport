<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Customer;
use App\Models\ProductSitePrice;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $pricesBySite = ProductSitePrice::query()
            ->with('product')
            ->get()
            ->groupBy('site_id');

        $customers->each(function (Customer $customer) use ($pricesBySite): void {
            $sitePrices = $pricesBySite->get($customer->site_id, collect());

            if ($sitePrices->isEmpty()) {
                return;
            }

            $ordersCount = random_int(5, 20);

            for ($i = 0; $i < $ordersCount; $i++) {
                $itemsCount = random_int(1, $sitePrices->count());
                $selectedPrices = $sitePrices->shuffle()->take($itemsCount);

                $total = 0;
                $itemsPayload = [];

                foreach ($selectedPrices as $sitePrice) {
                    $quantity = random_int(1, 3);
                    $unitPrice = (int) round(((float) $sitePrice->price) * 100);
                    $lineTotal = $unitPrice * $quantity;

                    $total += $lineTotal;

                    $itemsPayload[] = [
                        'product_id' => $sitePrice->product_id,
                        'product_name_snapshot' => $sitePrice->product->name,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'line_total' => $lineTotal,
                    ];
                }

                $status = fake()->randomElement(OrderStatus::cases());

                $paidAmount = match ($status) {
                    OrderStatus::Paid => $total,
                    OrderStatus::PartiallyPaid => max(0, $total - random_int(10, $total)),
                    default => 0,
                };

                $order = $customer->orders()->create([
                    'site_id' => $customer->site_id,
                    'status' => $status,
                    'payment_method' => fake()->randomElement(PaymentMethod::cases()),
                    'shipping_full_name' => $customer->name,
                    'shipping_full_address' => fake()->streetAddress(),
                    'shipping_city' => fake()->city(),
                    'shipping_country' => fake()->country(),
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                ]);

                $order->items()->createMany($itemsPayload);
            }
        });
    }
}
