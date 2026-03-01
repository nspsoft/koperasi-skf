<?php

namespace App\Services;

use App\Models\CreditInstallment;
use App\Models\Transaction;
use Carbon\Carbon;

class CreditInstallmentService
{
    public static function createSchedule(Transaction $transaction): void
    {
        if ($transaction->payment_method !== 'kredit' || ! $transaction->credit_tenor_months) {
            return;
        }

        $exists = CreditInstallment::where('transaction_id', $transaction->id)->exists();
        if ($exists) {
            return;
        }

        $tenor = (int) $transaction->credit_tenor_months;
        if ($tenor <= 0) {
            return;
        }

        $total = (float) $transaction->total_amount;
        $baseAmount = round($total / $tenor, 2);

        for ($i = 1; $i <= $tenor; $i++) {
            $amount = $i === $tenor
                ? round($total - ($baseAmount * ($tenor - 1)), 2)
                : $baseAmount;

            CreditInstallment::create([
                'transaction_id' => $transaction->id,
                'installment_number' => $i,
                'due_date' => Carbon::now()->addMonthsNoOverflow($i)->toDateString(),
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }
    }
}

