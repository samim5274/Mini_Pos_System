<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // fetch all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // 5 products for every tenant
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'tenant_id' => $tenant->id,
                    'name' => "Product {$i} for {$tenant->name}",
                    'sku' => strtoupper(substr($tenant->name,0,3)) . "-P{$i}", // tenant-wise unique SKU
                    'price' => rand(50, 500), // random price
                    'stock_quantity' => rand(10, 100),
                    'low_stock_threshold' => rand(5, 10),
                ]);
            }
        }
    }
}
