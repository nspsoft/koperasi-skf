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
            $table->string('purchase_unit', 50)->default('pcs')->after('unit'); // Satuan beli dari supplier
            $table->integer('conversion_factor')->default(1)->after('purchase_unit'); // Jumlah satuan jual per 1 satuan beli
            $table->decimal('margin_percent', 5, 2)->default(0)->after('conversion_factor'); // Target margin %
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_unit', 'conversion_factor', 'margin_percent']);
        });
    }
};
