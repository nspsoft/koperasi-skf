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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // INV-2023-001
            $table->string('category'); // Furniture, Electronics, Vehicle, Property, Other
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->integer('useful_life_years')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->string('condition')->default('good'); // good, damaged, lost, disposed
            $table->string('status')->default('active'); // active, written_off
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
