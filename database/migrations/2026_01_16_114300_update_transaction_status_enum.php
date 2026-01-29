<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM column to include 'processing' and 'ready'
        // Original: ['pending', 'paid', 'processing', 'completed', 'cancelled'] (Actually processing was already in migration file but maybe not in DB if added later?)
        // Let's ensure 'processing' and 'ready' are there.
        // Using raw SQL because Schema builder doesn't support modifying ENUM values easily on all drivers without Doctrine.
        
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values (Optional, but good practice)
        // Warning: This might fail if there are records with 'ready' status.
        // We will skip strict revert to avoid data loss during development.
    }
};
