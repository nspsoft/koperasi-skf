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
        Schema::table('generated_documents', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('data');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null')->after('reference_id');
            
            // Index for fast lookups
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_documents', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropForeign(['generated_by']);
            $table->dropColumn(['reference_type', 'reference_id', 'generated_by']);
        });
    }
};
