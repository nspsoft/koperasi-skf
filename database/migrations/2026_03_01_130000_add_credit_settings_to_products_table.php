<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_credit_eligible')->default(false)->after('is_preorder');
            $table->json('credit_tenors')->nullable()->after('is_credit_eligible');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_credit_eligible', 'credit_tenors']);
        });
    }
};

