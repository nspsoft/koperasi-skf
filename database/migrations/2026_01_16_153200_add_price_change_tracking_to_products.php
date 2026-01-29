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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('previous_cost', 15, 2)->nullable()->after('cost'); // Harga beli sebelumnya
            $table->integer('stock_at_old_cost')->default(0)->after('previous_cost'); // Qty stok saat harga lama
            $table->timestamp('cost_changed_at')->nullable()->after('stock_at_old_cost'); // Kapan harga berubah
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['previous_cost', 'stock_at_old_cost', 'cost_changed_at']);
        });
    }
};
