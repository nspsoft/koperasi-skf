<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Operasional Kantor',
                'description' => 'Biaya operasional kantor seperti sewa, kebersihan, dan keamanan'
            ],
            [
                'name' => 'Gaji & Tunjangan',
                'description' => 'Gaji karyawan, tunjangan, bonus, dan upah lembur'
            ],
            [
                'name' => 'Listrik & Air',
                'description' => 'Pembayaran tagihan listrik dan air'
            ],
            [
                'name' => 'Telepon & Internet',
                'description' => 'Biaya telekomunikasi, internet, dan pulsa'
            ],
            [
                'name' => 'ATK & Perlengkapan',
                'description' => 'Alat tulis kantor dan perlengkapan lainnya'
            ],
            [
                'name' => 'Konsumsi Rapat',
                'description' => 'Biaya konsumsi untuk rapat, pertemuan, dan acara'
            ],
            [
                'name' => 'Transport & BBM',
                'description' => 'Biaya transportasi, bahan bakar, dan parkir'
            ],
            [
                'name' => 'Pemeliharaan & Perbaikan',
                'description' => 'Biaya pemeliharaan dan perbaikan aset koperasi'
            ],
            [
                'name' => 'Pajak & Retribusi',
                'description' => 'Pembayaran pajak dan retribusi resmi'
            ],
            [
                'name' => 'Pendidikan & Pelatihan',
                'description' => 'Biaya pelatihan, seminar, dan pengembangan SDM'
            ],
            [
                'name' => 'Promosi & Marketing',
                'description' => 'Biaya promosi, iklan, dan marketing koperasi'
            ],
            [
                'name' => 'Lain-lain',
                'description' => 'Biaya operasional lainnya yang tidak termasuk kategori di atas'
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
