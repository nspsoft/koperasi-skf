<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DigitalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'produk-digital'],
            [
                'name' => 'Produk Digital',
                'slug' => 'produk-digital',
                'icon' => '📱',
                'description' => 'Pulsa, Token Listrik, dan E-Wallet'
            ]
        );

        $products = [
            // Pulsa & Data
            ['code' => 'P10', 'name' => 'Pulsa Telkomsel 10.000', 'price' => 12500, 'cost' => 10500],
            ['code' => 'P25', 'name' => 'Pulsa Telkomsel 25.000', 'price' => 27000, 'cost' => 25200],
            ['code' => 'P50', 'name' => 'Pulsa Telkomsel 50.000', 'price' => 52000, 'cost' => 50100],
            ['code' => 'P100', 'name' => 'Pulsa Telkomsel 100.000', 'price' => 101000, 'cost' => 99100],
            
            // PLN
            ['code' => 'PLN20', 'name' => 'Token Listrik PLN 20.000', 'price' => 22500, 'cost' => 20500],
            ['code' => 'PLN50', 'name' => 'Token Listrik PLN 50.000', 'price' => 52500, 'cost' => 50500],
            ['code' => 'PLN100', 'name' => 'Token Listrik PLN 100.000', 'price' => 102500, 'cost' => 100500],
            
            // Topup
            ['code' => 'GOPAY', 'name' => 'Topup GoPay 50.000', 'price' => 52000, 'cost' => 50500],
            ['code' => 'OVO', 'name' => 'Topup OVO 50.000', 'price' => 51500, 'cost' => 50500],
            ['code' => 'DANA', 'name' => 'Topup DANA 50.000', 'price' => 51000, 'cost' => 50500],
        ];

        foreach ($products as $p) {
            \App\Models\Product::updateOrCreate(
                ['code' => $p['code']],
                [
                    'category_id' => $category->id,
                    'name' => $p['name'],
                    'description' => 'Produk Digital ' . $p['name'],
                    'price' => $p['price'],
                    'cost' => $p['cost'],
                    'stock' => 9999, // Set high stock for digital items
                    'is_active' => true
                ]
            );
        }
    }
}
