<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditPaymentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    protected $paymentDate;
    protected $notes;

    public function __construct($paymentDate = null, $notes = null)
    {
        $this->paymentDate = $paymentDate ?? now()->toDateString();
        $this->notes = $notes ?? 'Pelunasan via payroll (import)';
    }

    public function model(array $row)
    {
        // Find transaction by invoice number
        $transaction = Transaction::where('invoice_number', $row['no_invoice'])
            ->where('status', 'credit')
            ->first();
        
        if (!$transaction) {
            return null; // Skip if not found or already paid
        }

        $amount = (float) str_replace(['.', ','], ['', '.'], $row['jumlah'] ?? $transaction->total_amount);

        // Process the payment via DB transaction
        DB::transaction(function () use ($transaction, $amount, $row) {
            // Update transaction status
            $transaction->update([
                'status' => 'completed',
                'paid_amount' => $transaction->total_amount,
                'notes' => $this->notes . 
                          ($row['keterangan'] ?? '') .
                          ' pada ' . now()->format('d/m/Y H:i'),
            ]);

            // Create journal entry for credit payment (via bank/transfer)
            \App\Services\JournalService::journalTransactionCreditPayment(
                $transaction, 
                $transaction->total_amount, 
                'transfer' // Always bank transfer for imported payments
            );
        });

        // Return null since we're updating, not creating
        return null;
    }

    public function rules(): array
    {
        return [
            'no_invoice' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'no_invoice.required' => 'Kolom no_invoice wajib diisi',
        ];
    }
}
