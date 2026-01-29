<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'credit' to the ENUM list.
        // Complete List: pending, paid, processing, ready, completed, cancelled, credit
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'ready', 'completed', 'cancelled', 'credit') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to list without 'credit' (Risky if data exists, but standard down procedure)
        // DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
