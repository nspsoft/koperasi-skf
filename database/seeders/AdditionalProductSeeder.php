<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdditionalProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'category' => [
                    'name' => 'Elektronik & Gadget',
                    'slug' => 'elektronik-gadget',
                    'icon' => '🎧',
                    'description' => 'Aksesori gadget dan peralatan elektronik kecil'
                ],
                'products' => [
                    ['code' => 'EL001', 'name' => 'Mouse Wireless Logitech M170', 'price' => 165000, 'cost' => 145000, 'stock' => 15],
                    ['code' => 'EL002', 'name' => 'Powerbank Robot 10000mAh', 'price' => 159000, 'cost' => 135000, 'stock' => 20],
                    ['code' => 'EL003', 'name' => 'Earphone Bluetooth Robot T20', 'price' => 125000, 'cost' => 105000, 'stock' => 10],
                    ['code' => 'EL004', 'name' => 'Lampu LED Hannochs 12W', 'price' => 35000, 'cost' => 28000, 'stock' => 40],
                ]
            ],
            [
                'category' => [
                    'name' => 'Kesehatan & Perawatan',
                    'slug' => 'kesehatan-perawatan',
                    'icon' => '💊',
                    'description' => 'Obat-obatan ringan dan perawatan diri'
                ],
                'products' => [
                    ['code' => 'KS001', 'name' => 'Tolak Angin Sachet (Box-12)', 'price' => 48000, 'cost' => 42000, 'stock' => 25],
                    ['code' => 'KS002', 'name' => 'Vitamin C Enervon-C (30 Tab)', 'price' => 42000, 'cost' => 38000, 'stock' => 15],
                    ['code' => 'KS003', 'name' => 'Minyak Kayu Putih Cap Lang 60ml', 'price' => 28000, 'cost' => 24000, 'stock' => 30],
                    ['code' => 'KS004', 'name' => 'Masker Sensi 3-Ply (50pcs)', 'price' => 35000, 'cost' => 25000, 'stock' => 50],
                ]
            ],
            [
                'category' => [
                    'name' => 'Peralatan Rumah Tangga',
                    'slug' => 'rumah-tangga',
                    'icon' => '🏠',
                    'description' => 'Kebutuhan peralatan pendukung rumah tangga'
                ],
                'products' => [
                    ['code' => 'RT001', 'name' => 'Maspion Setrika Listrik HA-110', 'price' => 145000, 'cost' => 125000, 'stock' => 8],
                    ['code' => 'RT002', 'name' => 'Miyako Magic Com 1.8L', 'price' => 285000, 'cost' => 255000, 'stock' => 5],
                    ['code' => 'RT003', 'name' => 'Teko Listrik Denpoo 1.5L', 'price' => 115000, 'cost' => 95000, 'stock' => 12],
                    ['code' => 'RT004', 'name' => 'Handuk Mandi Terry Palmer', 'price' => 85000, 'cost' => 70000, 'stock' => 20],
                ]
            ]
        ];

        foreach ($data as $item) {
            $category = \App\Models\Category::firstOrCreate(
                ['slug' => $item['category']['slug']],
                $item['category']
            );

            foreach ($item['products'] as $p) {
                \App\Models\Product::firstOrCreate(
                    ['code' => $p['code']],
                    [
                        'category_id' => $category->id,
                        'name' => $p['name'],
                        'description' => 'Produk baru kategori ' . $category->name,
                        'price' => $p['price'],
                        'cost' => $p['cost'],
                        'stock' => $p['stock'],
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
