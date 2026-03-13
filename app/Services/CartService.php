<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Cache\Repository;
use Illuminate\Container\Attributes\Cache;
use Illuminate\Support\Arr;

class CartService
{
    public function __construct(
        #[Cache] protected Repository $cache,
    ) {}

    public function get(string $token): array
    {
        return $this->cache->get($token, ['items' => []]);
    }

    public function put(string $token, array $cart): void
    {
        $ttl = config('cart.cache_ttl');

        $this->cache->put($token, $cart, $ttl);
    }

    public function add(string $token, Product $product, int $quantity = 1): array
    {
        $product->loadMissing('sitePrice');

        $cart = $this->get($token);

        $productExists = Arr::has($cart, "items.{$product->id}");

        if ($productExists) {
            $cart['items'][$product->id]['quantity'] += $quantity;
        } else {
            $cart['items'][$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit_price' => $product->sitePrice->price,
                'quantity' => $quantity,
            ];
        }

        $this->put($token, $cart);

        return $cart;
    }

    public function update(string $token, array $items): array
    {
        $cart = $this->get($token);

        foreach ($items as $item) {
            $productId = Arr::get($item, 'product_id');
            $productExists = Arr::has($cart, "items.{$productId}");

            if (!$productExists) {
                continue;
            }

            $quantity = Arr::get($item, 'quantity', 0);

            if ($quantity > 0) {
                $cart['items'][$productId]['quantity'] = $quantity;
            } else {
                Arr::forget($cart, "items.{$productId}");
            }
        }

        $this->put($token, $cart);

        return $cart;
    }

    public function updateQuantity(string $token, int $productId, int $quantity): array
    {
        $cart = $this->get($token);

        $productExists = Arr::has($cart, "items.{$productId}");

        if (!$productExists) {
            return $cart;
        }

        if ($quantity > 0) {
            $cart['items'][$productId]['quantity'] = $quantity;
        } else {
            Arr::forget($cart, "items.{$productId}");
        }

        $this->put($token, $cart);

        return $cart;
    }

    public function remove(string $token, int $productId): array
    {
        $cart = $this->get($token);

        Arr::forget($cart, "items.{$productId}");

        $this->put($token, $cart);

        return $cart;
    }

    public function clear(string $token): void
    {
        $this->cache->forget($token);
    }
}
