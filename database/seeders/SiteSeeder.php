<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            ['code' => 'fr', 'name' => 'NutriSport France', 'domain' => 'fr.localhost'],
            ['code' => 'it', 'name' => 'NutriSport Italia', 'domain' => 'it.localhost'],
            ['code' => 'be', 'name' => 'NutriSport Belgium', 'domain' => 'be.localhost'],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
