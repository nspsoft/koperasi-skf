<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle Midtrans payment notification webhook
     */
    public function handle(Request $request)
    {
        // Log the incoming webhook
        Log::info('Payment Webhook Received', $request->all());

        $serverKey = Setting::get('midtrans_server_key', '');
        
        if (empty($serverKey)) {
            Log::error('Payment Webhook: Server key not configured');
            return response()->json(['status' => 'error', 'message' => 'not configured'], 500);
        }

        // Get notification data
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status ?? 'accept';
        $signatureKey = $request->signature_key;

        // Verify signature
        $expectedSignature = hash('sha512', 
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        if ($signatureKey !== $expectedSignature) {
            Log::error('Payment Webhook: Invalid signature', [
                'order_id' => $orderId,
                'expected' => $expectedSignature,
                'received' => $signatureKey
            ]);
            return response()->json(['status' => 'error', 'message' => 'invalid signature'], 403);
        }

        // Find transaction by invoice number
        $transaction = Transaction::where('invoice_number', $orderId)->first();

        if (!$transaction) {
            Log::warning('Payment Webhook: Transaction not found', ['order_id' => $orderId]);
            return response()->json(['status' => 'error', 'message' => 'transaction not found'], 404);
        }

        // Process based on transaction status
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'accept') {
                // Payment successful
                $this->handlePaymentSuccess($transaction, $request->all());
            }
        } elseif ($transactionStatus == 'pending') {
            // Payment pending
            $this->handlePaymentPending($transaction);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Payment failed
            $this->handlePaymentFailed($transaction, $transactionStatus);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess($transaction, $notificationData)
    {
        Log::info('Payment Success', ['invoice' => $transaction->invoice_number]);

        // Update transaction status
        $transaction->update([
            'status' => 'completed',
            'paid_amount' => $transaction->total_amount,
            'payment_method' => $notificationData['payment_type'] ?? $transaction->payment_method,
            'notes' => 'Paid via Midtrans - ' . ($notificationData['transaction_id'] ?? ''),
        ]);

        // Create journal entry for payment
        try {
            $transaction->load('items.product');
            \App\Services\JournalService::journalSale($transaction);
        } catch (\Exception $e) {
            Log::error('Payment Webhook: Failed to create journal', [
                'invoice' => $transaction->invoice_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle pending payment
     */
    private function handlePaymentPending($transaction)
    {
        Log::info('Payment Pending', ['invoice' => $transaction->invoice_number]);
        
        $transaction->update([
            'status' => 'pending',
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($transaction, $reason)
    {
        Log::info('Payment Failed', ['invoice' => $transaction->invoice_number, 'reason' => $reason]);
        
        $transaction->update([
            'status' => 'failed',
            'notes' => 'Payment ' . $reason,
        ]);
    }
}
