<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Meeting;
use App\Models\TeamMember;
use App\Models\WorkProgram;
use Carbon\Carbon;

class KepengurusanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Template Dokumen & Panduan Pendirian
        $this->call([
            DocumentTemplateSeeder::class,
            EstablishmentDocumentSeeder::class,
        ]);

        // 2. Inventaris Aset
        Asset::updateOrCreate(['code' => 'AST-001'], [
            'name' => 'Laptop Lenovo Thinkpad',
            'category' => 'electronics',
            'purchase_date' => Carbon::now()->subYear()->subMonths(2),
            'purchase_price' => 7500000,
            'useful_life_years' => 4,
            'current_value' => 5000000,
            'location' => 'Kantor Koperasi',
            'condition' => 'good',
            'status' => 'active',
            'description' => 'Aset inventaris untuk admin',
        ]);

        Asset::updateOrCreate(['code' => 'AST-002'], [
            'name' => 'Printer Epson L3110',
            'category' => 'electronics',
            'purchase_date' => Carbon::now()->subYear()->subMonths(1),
            'purchase_price' => 2500000,
            'useful_life_years' => 3,
            'current_value' => 1500000,
            'location' => 'Kantor Koperasi',
            'condition' => 'good',
            'status' => 'active',
        ]);

        Asset::updateOrCreate(['code' => 'AST-003'], [
            'name' => 'Meja Kerja & Kursi',
            'category' => 'furniture',
            'purchase_date' => Carbon::now()->subYear()->subMonths(5),
            'purchase_price' => 1200000,
            'useful_life_years' => 5,
            'current_value' => 1000000,
            'location' => 'Ruang Tamu',
            'condition' => 'fair',
            'status' => 'active',
        ]);

        // 3. Notulen Rapat
        Meeting::updateOrCreate(['title' => 'Rapat Anggota Tahunan 2024'], [
            'type' => 'rat',
            'scheduled_at' => Carbon::now()->subYear()->subMonths(3),
            'location' => 'Aula Utama PT. SPINDO',
            'agenda' => 'Laporan Pertanggungjawaban Pengurus & Pengawas',
            'notes' => '1. Seluruh laporan diterima dengan baik oleh anggota. 2. Pembagian SHU disetujui sebesar 40% dari profit.',
            'status' => 'completed',
            'created_by' => 1,
        ]);

        Meeting::updateOrCreate(['title' => 'Rapat Evaluasi Bulanan Pengurus'], [
            'type' => 'rutin',
            'scheduled_at' => Carbon::now()->subYear()->addDays(5),
            'location' => 'Ruang Rapat Koperasi',
            'agenda' => 'Evaluasi Kinerja dan Penjualan Toko',
            'status' => 'scheduled',
            'created_by' => 1,
        ]);

        // 4. Daftar Pengurus (TeamMember)
        TeamMember::updateOrCreate(['name' => 'Bapak Budi Santoso'], [
            'role' => 'Ketua Koperasi',
            'bio' => 'Memimpin koperasi sejak tahun 2021 dengan penuh dedikasi tinggi.',
            'order' => 1,
        ]);

        TeamMember::updateOrCreate(['name' => 'Ibu Siti Aminah'], [
            'role' => 'Bendahara Umum',
            'bio' => 'Mengelola keuangan koperasi dan pembukuan akuntansi.',
            'order' => 2,
        ]);

        TeamMember::updateOrCreate(['name' => 'Bapak Joko Susilo'], [
            'role' => 'Ketua Pengawas',
            'bio' => 'Bertanggung jawab melakukan audit internal kegiatan operasional koperasi.',
            'order' => 3,
        ]);

        // 5. Tugas & Wewenang (WorkProgram)
        WorkProgram::updateOrCreate(['title' => 'Unit Simpan Pinjam'], [
            'description' => 'Program utama koperasi yang memfasilitasi kebutuhan dana darurat dan pinjaman anggota dengan bunga ringan.',
            'icon' => 'fas fa-wallet',
            'color' => '#4f46e5',
            'order' => 1,
        ]);

        WorkProgram::updateOrCreate(['title' => 'Unit Toko & Konsinyasi'], [
            'description' => 'Penyediaan kebutuhan pokok anggota sehari-hari dan fasilitas titip jual (konsinyasi) bagi anggota yang memiliki usaha.',
            'icon' => 'fas fa-store',
            'color' => '#10b981',
            'order' => 2,
        ]);
        
        $this->command->info('✅ Data Dummy Kepengurusan berhasil ditambahkan!');
    }
}
