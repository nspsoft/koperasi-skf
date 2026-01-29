<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('ai_settings')->insert([
            ['key' => 'ai_enabled', 'value' => 'true', 'description' => 'Enable/disable AI Assistant', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_provider', 'value' => 'ollama', 'description' => 'AI Provider (ollama, openai, custom)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_url', 'value' => 'http://localhost:11434', 'description' => 'AI API Endpoint URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_model', 'value' => 'llama2', 'description' => 'Default AI Model', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_api_key', 'value' => '', 'description' => 'API Key for OpenAI or other providers', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_system_prompt', 'value' => 'Kamu adalah AI Assistant untuk aplikasi Koperasi Karyawan. Bantu anggota dengan pertanyaan tentang simpanan, pinjaman, belanja, dan SHU. Jawab dengan bahasa Indonesia yang ramah.', 'description' => 'System prompt for AI context', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
