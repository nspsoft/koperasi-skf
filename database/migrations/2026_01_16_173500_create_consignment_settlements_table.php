<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('consignor_type'); // 'member', 'supplier'
            $table->unsignedBigInteger('consignor_id');
            
            $table->date('period_start');
            $table->date('period_end');
            
            $table->decimal('total_sales_amount', 15, 2)->default(0); // Total omzet (Sales)
            $table->decimal('total_payable_amount', 15, 2)->default(0); // Yang harus dibayar ke mitra (COGS)
            $table->decimal('total_profit_amount', 15, 2)->default(0); // Keuntungan koperasi
            
            $table->string('status')->default('draft'); // draft, paid
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable(); // Admin user id
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['consignor_type', 'consignor_id']);
        });

        // Add settlement_id to transaction_items to track which items have been settled
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreignId('consignment_settlement_id')->nullable()->constrained('consignment_settlements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['consignment_settlement_id']);
            $table->dropColumn('consignment_settlement_id');
        });
        Schema::dropIfExists('consignment_settlements');
    }
};
