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
        if (!Schema::hasColumn('members', 'phone')) {
            Schema::table('members', function (Blueprint $table) {
                if (Schema::hasColumn('members', 'address')) {
                    $table->string('phone', 20)->nullable()->after('address');
                } else {
                    $table->string('phone', 20)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
