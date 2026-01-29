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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('member_id')->unique(); // NIK Anggota
            $table->string('employee_id')->nullable(); // NIK Karyawan
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('join_date');
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->text('address')->nullable();
            $table->string('id_card_number')->nullable(); // KTP
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
