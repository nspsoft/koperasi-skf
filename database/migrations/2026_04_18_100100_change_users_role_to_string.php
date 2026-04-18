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
        Schema::table('users', function (Blueprint $table) {
            // Change role column from ENUM to STRING to support dynamic roles from roles table
            $table->string('role')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to ENUM (This might fail if there are values not in the old list)
            $table->enum('role', ['member', 'admin', 'pengurus'])->default('member')->change();
        });
    }
};
