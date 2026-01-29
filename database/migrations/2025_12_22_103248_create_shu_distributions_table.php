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
        Schema::create('shu_distributions', function (Blueprint $table) {
            $table->id();
            $table->year('period_year');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('total_savings', 15, 2)->default(0); // Member's total savings contribution
            $table->decimal('total_transactions', 15, 2)->default(0); // Member's total transactions (purchases)
            $table->decimal('total_loans', 15, 2)->default(0); // Member's loan interest paid (jasa)
            $table->decimal('shu_savings', 15, 2)->default(0); // SHU from savings portion
            $table->decimal('shu_transactions', 15, 2)->default(0); // SHU from transactions portion
            $table->decimal('shu_jasa', 15, 2)->default(0); // SHU from loan interest (jasa) portion
            $table->decimal('total_shu', 15, 2)->default(0); // Total SHU allocation
            $table->enum('status', ['calculated', 'distributed'])->default('calculated');
            $table->timestamp('distributed_at')->nullable();
            $table->foreignId('calculated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['period_year', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shu_distributions');
    }
};
