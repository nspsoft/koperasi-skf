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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama dokumen (e.g. Surat Keterangan Anggota)
            $table->string('type'); // Tipe/Category (e.g. membership, loan, official)
            $table->text('content'); // HTML content with placeholders
            $table->json('placeholders')->nullable(); // List of expected placeholders
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
