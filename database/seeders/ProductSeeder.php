<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Whey Protein'],
            ['name' => 'Creatine Monohydrate'],
            ['name' => 'BCAA'],
            ['name' => 'Mass Gainer'],
            ['name' => 'Pre Workout'],
            ['name' => 'Glutamine'],
            ['name' => 'Omega 3'],
            ['name' => 'Multivitamin'],
            ['name' => 'Casein Protein'],
            ['name' => 'L-Carnitine'],
            ['name' => 'ZMA'],
            ['name' => 'Vitamin D3'],
            ['name' => 'Collagen Powder'],
            ['name' => 'Electrolyte Powder'],
            ['name' => 'Nitric Oxide Booster'],
            ['name' => 'Caffeine Capsules'],
            ['name' => 'Ashwagandha'],
            ['name' => 'Magnesium'],
            ['name' => 'Zinc'],
            ['name' => 'Test Booster'],
            ['name' => 'Recovery Drink'],
            ['name' => 'Meal Replacement Shake'],
        ];

        foreach ($products as $product) {
            Product::create([
                'stock' => rand(0, 100),
                ...$product,
            ]);
        }
    }
}
