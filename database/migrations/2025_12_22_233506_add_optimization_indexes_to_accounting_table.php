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
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index('department');
            $table->index('position');
        });

        Schema::table('savings', function (Blueprint $table) {
            $table->index('type');
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['department']);
            $table->dropIndex(['position']);
        });

        Schema::table('savings', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['transaction_type']);
        });
    }
};
