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
        Schema::create('payroll_external_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('period', 7); // YYYY-MM e.g. '2026-10'
            $table->string('source', 50)->default('amigo'); // 'amigo', etc.
            $table->string('employee_id', 50); // NIK / No Absen e.g. 'KA 008'
            $table->string('employee_name')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['period', 'source', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_external_deductions');
    }
};
