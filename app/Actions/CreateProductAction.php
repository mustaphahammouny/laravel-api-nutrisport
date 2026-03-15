<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function handle(array $data): Product
    {
        $prices = Arr::get($data, 'prices');

        foreach ($prices as $price) {
            $siteId = Arr::get($price, 'site_id');

            $sitePrices[$siteId] = [
                'price' => Arr::get($price, 'price'),
            ];
        }

        return DB::transaction(function () use ($data, $sitePrices) {
            $product = Product::create([
                'name' => Arr::get($data, 'name'),
                'stock' => Arr::get($data, 'stock'),
            ]);

            $product->sites()->attach($sitePrices);

            return $product;
        });
    }
}
