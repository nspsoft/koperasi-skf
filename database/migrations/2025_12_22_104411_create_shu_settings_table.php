<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shu_settings', function (Blueprint $table) {
            $table->id();
            $table->year('period_year')->unique();
            $table->decimal('total_shu_pool', 15, 2)->default(0); // Total SHU yang akan dibagi
            
            // Alokasi Dana (dalam persen)
            $table->decimal('persen_cadangan', 5, 2)->default(25); // Dana Cadangan (min 20% sesuai UU)
            $table->decimal('persen_jasa_modal', 5, 2)->default(30); // Jasa Modal/Simpanan untuk Anggota
            $table->decimal('persen_jasa_usaha', 5, 2)->default(25); // Jasa Usaha/Transaksi untuk Anggota
            $table->decimal('persen_pengurus', 5, 2)->default(5); // Dana Pengurus
            $table->decimal('persen_karyawan', 5, 2)->default(5); // Dana Karyawan
            $table->decimal('persen_pendidikan', 5, 2)->default(5); // Dana Pendidikan Koperasi
            $table->decimal('persen_sosial', 5, 2)->default(2.5); // Dana Sosial
            $table->decimal('persen_pembangunan', 5, 2)->default(2.5); // Dana Pembangunan Daerah
            
            // Hasil perhitungan pool per komponen
            $table->decimal('pool_cadangan', 15, 2)->default(0);
            $table->decimal('pool_jasa_modal', 15, 2)->default(0);
            $table->decimal('pool_jasa_usaha', 15, 2)->default(0);
            $table->decimal('pool_pengurus', 15, 2)->default(0);
            $table->decimal('pool_karyawan', 15, 2)->default(0);
            $table->decimal('pool_pendidikan', 15, 2)->default(0);
            $table->decimal('pool_sosial', 15, 2)->default(0);
            $table->decimal('pool_pembangunan', 15, 2)->default(0);
            
            $table->enum('status', ['draft', 'calculated', 'distributed'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shu_settings');
    }
};
