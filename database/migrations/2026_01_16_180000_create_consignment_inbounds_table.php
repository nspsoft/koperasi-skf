<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('consignor_type'); // 'member', 'supplier'
            $table->unsignedBigInteger('consignor_id');
            $table->date('inbound_date');
            $table->text('note')->nullable();
            
            $table->string('status')->default('completed'); // 'draft', 'completed'
            $table->unsignedBigInteger('created_by');
            
            $table->timestamps();
            
            $table->index(['consignor_type', 'consignor_id']);
        });

        Schema::create('consignment_inbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_inbound_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2); // Snapshot of cost at receive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_inbound_items');
        Schema::dropIfExists('consignment_inbounds');
    }
};
