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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // TRX-YYYYMMDD-XXXX
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Pembeli (Member/User)
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null'); // Kasir
            $table->enum('type', ['offline', 'online'])->default('offline');
            $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // cash, transfer, saldo_sukarela
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
