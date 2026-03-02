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

        $baseDate = $transaction->created_at ? Carbon::parse($transaction->created_at) : Carbon::now();

        for ($i = 1; $i <= $tenor; $i++) {
            $amount = $i === $tenor
                ? round($total - ($baseAmount * ($tenor - 1)), 2)
                : $baseAmount;

            CreditInstallment::create([
                'transaction_id' => $transaction->id,
                'installment_number' => $i,
                'due_date' => $baseDate->copy()->addMonthsNoOverflow($i - 1)->toDateString(),
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }
    }

    public static function rebuildSchedule(Transaction $transaction): void
    {
        if ($transaction->payment_method !== 'kredit' || ! $transaction->credit_tenor_months) {
            return;
        }

        $transaction->load('creditInstallments');

        if ($transaction->creditInstallments->where('status', 'paid')->isNotEmpty()) {
            return;
        }

        $transaction->creditInstallments()->delete();
        self::createSchedule($transaction);
    }
}
