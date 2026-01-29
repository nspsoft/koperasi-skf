<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CommerceSeeder extends Seeder
{
    public function run()
    {
        // define Categories
        $categories = [
            [
                'name' => 'Makanan Ringan',
                'slug' => 'makanan-ringan',
                'icon' => '🍿',
                'description' => 'Aneka snack dan camilan'
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'icon' => '🥤',
                'description' => 'Minuman segar dan kemasan'
            ],
            [
                'name' => 'Sembako',
                'slug' => 'sembako',
                'icon' => '🍚',
                'description' => 'Beras, Minyak, Gula, dll'
            ],
            [
                'name' => 'ATK',
                'slug' => 'atk',
                'icon' => '✏️',
                'description' => 'Alat Tulis Kantor'
            ],
            [
                'name' => 'Kebersihan',
                'slug' => 'kebersihan',
                'icon' => '🧹',
                'description' => 'Sabun, Deterjen, dll'
            ]
        ];

        foreach ($categories as $cat) {
            $category = Category::create($cat);
            $this->createProducts($category);
        }
    }

    private function createProducts($category)
    {
        $products = [];

        switch ($category->slug) {
            case 'makanan-ringan':
                $products = [
                    ['code' => 'MR001', 'name' => 'Chitato Sapi Panggang 68g', 'price' => 11500, 'cost' => 10000, 'stock' => 50],
                    ['code' => 'MR002', 'name' => 'Oreo Original 133g', 'price' => 9500, 'cost' => 8000, 'stock' => 60],
                    ['code' => 'MR003', 'name' => 'Qtela Singkong Balado', 'price' => 15000, 'cost' => 13500, 'stock' => 40],
                    ['code' => 'MR004', 'name' => 'Beng Beng Wafer', 'price' => 2500, 'cost' => 2000, 'stock' => 100],
                    ['code' => 'MR005', 'name' => 'Silverqueen Chunky Bar', 'price' => 22000, 'cost' => 18000, 'stock' => 25],
                ];
                break;
            case 'minuman':
                $products = [
                    ['code' => 'MN001', 'name' => 'Teh Botol Sosro 450ml', 'price' => 6000, 'cost' => 4500, 'stock' => 70],
                    ['code' => 'MN002', 'name' => 'Aqua Botol 600ml', 'price' => 4000, 'cost' => 3000, 'stock' => 100],
                    ['code' => 'MN003', 'name' => 'Coca Cola 390ml', 'price' => 5500, 'cost' => 4200, 'stock' => 40],
                    ['code' => 'MN004', 'name' => 'Pocari Sweat 500ml', 'price' => 8000, 'cost' => 6500, 'stock' => 30],
                    ['code' => 'MN005', 'name' => 'Kopi Kapal Api Botol', 'price' => 5000, 'cost' => 3800, 'stock' => 50],
                    ['code' => 'MN006', 'name' => 'Susu Bear Brand', 'price' => 12000, 'cost' => 10500, 'stock' => 90],
                ];
                break;
            case 'sembako':
                $products = [
                    ['code' => 'SB001', 'name' => 'Beras Pandan Wangi 5kg', 'price' => 85000, 'cost' => 78000, 'stock' => 20],
                    ['code' => 'SB002', 'name' => 'Minyak Goreng Sania 2L', 'price' => 38000, 'cost' => 35000, 'stock' => 30],
                    ['code' => 'SB003', 'name' => 'Gula Pasir Gulaku 1kg', 'price' => 16500, 'cost' => 15000, 'stock' => 45],
                    ['code' => 'SB004', 'name' => 'Telur Ayam 1kg', 'price' => 28000, 'cost' => 25000, 'stock' => 10],
                    ['code' => 'SB005', 'name' => 'Kecap Bango Manis 550ml', 'price' => 24000, 'cost' => 21000, 'stock' => 25],
                    ['code' => 'SB006', 'name' => 'Indomie Goreng (Kardus)', 'price' => 110000, 'cost' => 105000, 'stock' => 15],
                ];
                break;
            case 'atk':
                $products = [
                    ['code' => 'AT001', 'name' => 'Buku Tulis Sidu 38 Lembar', 'price' => 4500, 'cost' => 3000, 'stock' => 100],
                    ['code' => 'AT002', 'name' => 'Pulpen Standard AE7', 'price' => 2500, 'cost' => 1500, 'stock' => 200],
                    ['code' => 'AT003', 'name' => 'Kertas A4 Sidu 70gsm (Rim)', 'price' => 45000, 'cost' => 40000, 'stock' => 30],
                    ['code' => 'AT004', 'name' => 'Isi Staples No. 10', 'price' => 2000, 'cost' => 1000, 'stock' => 50],
                ];
                break;
            case 'kebersihan':
                $products = [
                    ['code' => 'KB001', 'name' => 'Rinso Anti Noda 700g', 'price' => 22000, 'cost' => 19000, 'stock' => 30],
                    ['code' => 'KB002', 'name' => 'Sunlight Jeruk Nipis 780ml', 'price' => 15000, 'cost' => 12500, 'stock' => 40],
                    ['code' => 'KB003', 'name' => 'Lifebuoy Body Wash 450ml', 'price' => 25000, 'cost' => 21000, 'stock' => 25],
                    ['code' => 'KB004', 'name' => 'Pasta Gigi Pepsodent 120g', 'price' => 11000, 'cost' => 9000, 'stock' => 60],
                ];
                break;
        }

        foreach ($products as $p) {
            Product::create([
                'category_id' => $category->id,
                'code' => $p['code'],
                'name' => $p['name'],
                'description' => 'Deskripsi untuk ' . $p['name'],
                'price' => $p['price'],
                'cost' => $p['cost'],
                'stock' => $p['stock'],
                'is_active' => true
            ]);
        }
    }
}
