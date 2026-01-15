<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            'Alpha Store',
            'Beta Mart',
            'Gamma Shop',
            'Delta Electronics',
            'Epsilon Grocery',
            'Zeta Fashion',
            'Eta Hardware',
            'Theta Supplies',
            'Iota Market',
            'Kappa Cafe',
        ];

        foreach ($tenants as $name) {
            Tenant::create([
                'name' => $name,
            ]);
        }
    }
}
