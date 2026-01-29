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
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->string('payment_number');
            $table->integer('installment_number'); // Angsuran ke-
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_amount', 15, 2); // Pokok
            $table->decimal('interest_amount', 15, 2); // Bunga
            $table->date('due_date'); // Tanggal jatuh tempo
            $table->date('payment_date')->nullable(); // Tanggal bayar
            $table->enum('status', ['pending', 'paid', 'overdue', 'partial'])->default('pending');
            $table->enum('payment_method', ['cash', 'transfer', 'salary_deduction'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
