<?php

namespace App\Http\Controllers;

use App\Models\CreditInstallment;
use App\Models\Saving;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditInstallmentController extends Controller
{
    public function pay(Request $request, CreditInstallment $installment)
    {
        if ($installment->status === 'paid') {
            return back()->with('error', 'Cicilan ini sudah lunas.');
        }

        $user = auth()->user();
        $member = $user->member;
        $transaction = $installment->transaction;

        // Security check
        if ($transaction->user_id !== $user->id && !$user->hasPermission('manage_credits')) {
            abort(403);
        }

        $paymentMethod = $request->input('payment_method', 'saldo_sukarela');

        // Access check for sensitive payment methods
        if (in_array($paymentMethod, ['cash', 'bank']) && !$user->hasPermission('manage_credits')) {
            return back()->with('error', 'Hanya Admin yang dapat menginput pembayaran Tunai/Bank.');
        }

        try {
            DB::beginTransaction();

            if ($paymentMethod === 'saldo_sukarela') {
                if (!$member || $member->balance < $installment->amount) {
                    throw new \Exception('Saldo Sukarela anggota tidak mencukupi.');
                }

                // Create Saving Withdrawal
                $saving = Saving::create([
                    'member_id' => $member->id,
                    'type' => 'sukarela',
                    'transaction_date' => now(),
                    'transaction_type' => 'withdrawal',
                    'amount' => $installment->amount,
                    'description' => "Bayar Cicilan #{$installment->installment_number} - {$transaction->invoice_number}",
                    'created_by' => $user->id,
                ]);

                JournalService::journalSavingWithdrawal($saving, 'saldo');
            }

            // Update Installment
            $installment->update([
                'status' => 'paid',
                'paid_at' => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now(),
                'payment_method' => $paymentMethod,
                'notes' => $request->notes,
            ]);

            // Update Parent Transaction
            $transaction->increment('paid_amount', $installment->amount);
            
            // Check if all installments are paid
            $remaining = $transaction->creditInstallments()->where('status', 'pending')->count();
            if ($remaining === 0) {
                $transaction->update(['status' => 'completed']);
            }

            // Create Journal for Credit Payment
            JournalService::journalTransactionCreditPayment($transaction, $installment->amount, $paymentMethod);

            DB::commit();

            return back()->with('success', 'Cicilan berhasil dibayar!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
