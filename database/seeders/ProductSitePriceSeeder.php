<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Site;
use Illuminate\Database\Seeder;

class ProductSitePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();
        $products = Product::all();

        $sites->each(function (Site $site) use ($products) {
            $productPrices = $products->reduce(
                function (array $carry, Product $product) {
                    $carry[$product->id] = ['price' => rand(20, 200)];

                    return $carry;
                },
                []
            );

            $site->products()->attach($productPrices);
        });
    }
}
