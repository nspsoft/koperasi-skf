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
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position'); // Ketua, Wakil Ketua, Sekretaris, Bendahara, Pengawas, Manager
            $table->string('department')->nullable(); // Operasional, Usaha, Administrasi
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('period')->nullable(); // 2023-2028
            $table->string('status')->default('active'); // active, inactive, demisioner
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
