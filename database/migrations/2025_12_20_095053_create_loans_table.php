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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->enum('loan_type', ['regular', 'emergency', 'education', 'special'])->default('regular');
            $table->decimal('amount', 15, 2); // Jumlah pinjaman
            $table->decimal('interest_rate', 5, 2)->default(1.00); // Bunga per bulan (%)
            $table->integer('duration_months'); // Tenor dalam bulan
            $table->decimal('monthly_installment', 15, 2); // Angsuran bulanan
            $table->decimal('total_amount', 15, 2); // Total yang harus dibayar
            $table->decimal('remaining_amount', 15, 2); // Sisa pinjaman
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'completed', 'defaulted'])->default('pending');
            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->date('disbursement_date')->nullable(); // Tanggal pencairan
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo
            $table->text('purpose')->nullable(); // Tujuan pinjaman
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
