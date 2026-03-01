<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('credit_tenor_months')->nullable()->after('payment_method');
            $table->decimal('credit_installment_amount', 15, 2)->nullable()->after('credit_tenor_months');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['credit_tenor_months', 'credit_installment_amount']);
        });
    }
};

