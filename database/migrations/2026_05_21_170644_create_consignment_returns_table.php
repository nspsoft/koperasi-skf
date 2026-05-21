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
        Schema::create('consignment_returns', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('return_date');
            $table->string('consignor_type'); // 'member' or 'supplier'
            $table->unsignedBigInteger('consignor_id');
            $table->integer('total_items')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_returns');
    }
};
