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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->string('description');
            $table->decimal('amount', 15, 2); // Absolute amount
            $table->enum('type', ['debit', 'credit']); // debit = money in (in accounting specific context this might be flipped depending on perspective, but typically for bank statement: CR = IN, DR = OUT. However, we will store logical flow: credit=IN, debit=OUT to match bank statement typical behavior, OR we standardize to accounting: Debit = Increase Asset (Money In), Credit = Decrease Asset (Money Out). Let's stick to Bank Statement convention which acts opposite to company books, OR normalize it. 
            // Better: 'in' (deposit), 'out' (withdrawal) to avoid confusion.
            // Let's use 'debit' and 'credit' as per standard bank statement import.
            // In Bank Statement: Credit = Money In, Debit = Money Out.
            
            $table->enum('status', ['pending', 'reconciled', 'ignored'])->default('pending');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            
            // Metadata for tracking who imported it
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
