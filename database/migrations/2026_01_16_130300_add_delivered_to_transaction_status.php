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
        // Add 'delivered' to the ENUM list.
        // Complete List: pending, paid, processing, ready, completed, cancelled, credit, delivered
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'ready', 'completed', 'cancelled', 'credit', 'delivered') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'ready', 'completed', 'cancelled', 'credit') DEFAULT 'pending'");
    }
};
