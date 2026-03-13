<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();

        $sites->each(fn(Site $site) => $site->customers()
            ->create([
                'name' => "Customer {$site->code}",
                'email' => "customer-{$site->code}@example.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]));
    }
}
