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
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number');
            $table->string('document_type');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('data')->nullable(); // Snapshot of placeholders
            $table->timestamp('verified_at')->nullable(); // When scanned
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
