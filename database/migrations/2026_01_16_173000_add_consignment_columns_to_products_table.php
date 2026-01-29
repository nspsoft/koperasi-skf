<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_consignment')->default(false)->after('description');
            $table->string('consignor_type')->nullable()->after('is_consignment'); // 'member', 'supplier'
            $table->unsignedBigInteger('consignor_id')->nullable()->after('consignor_type');
            $table->decimal('consignment_price', 15, 2)->nullable()->after('consignor_id'); // Harga setor ke mitra
            $table->decimal('consignment_profit_percent', 5, 2)->nullable()->after('consignment_price'); // Optional: % bagi hasil
            
            $table->index(['consignor_type', 'consignor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_consignment', 
                'consignor_type', 
                'consignor_id', 
                'consignment_price',
                'consignment_profit_percent'
            ]);
        });
    }
};
