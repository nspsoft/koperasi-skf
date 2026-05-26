<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendLoanReminders extends Command
{
    protected $signature = 'email:loan-reminders {--days=3 : Hari sebelum jatuh tempo untuk pengingat} {--overdue : Kirim juga ke yang sudah lewat jatuh tempo}';
    protected $description = 'Kirim email pengingat cicilan pinjaman ke anggota';

    public function handle()
    {
        $daysBefore = (int) $this->option('days');
        $includeOverdue = $this->option('overdue');

        $coopName = Setting::get('coop_name', 'Koperasi Karyawan SKF');
        $coopEmail = Setting::get('coop_email', '');
        $coopPhone = Setting::get('coop_phone', '');
        $coopAddress = Setting::get('coop_address', '');

        // Build signature
        $signature = "\n\n--\nHormat kami,\n{$coopName}\n";
        if ($coopAddress) $signature .= "📍 {$coopAddress}\n";
        if ($coopPhone) $signature .= "📞 {$coopPhone}\n";
        if ($coopEmail) $signature .= "📧 {$coopEmail}\n";

        $sentCount = 0;
        $errorCount = 0;

        // 1. Upcoming payments (due within $daysBefore days)
        $upcomingPayments = LoanPayment::with(['loan.member.user'])
            ->whereIn('status', ['pending'])
            ->whereBetween('due_date', [now(), now()->addDays($daysBefore)])
            ->get();

        foreach ($upcomingPayments as $payment) {
            $result = $this->sendReminder($payment, 'upcoming', $coopName, $signature);
            if ($result) $sentCount++;
            else $errorCount++;
        }

        // 2. Overdue payments
        if ($includeOverdue) {
            $overduePayments = LoanPayment::with(['loan.member.user'])
                ->whereIn('status', ['pending', 'overdue'])
                ->where('due_date', '<', now())
                ->get();

            foreach ($overduePayments as $payment) {
                $result = $this->sendReminder($payment, 'overdue', $coopName, $signature);
                if ($result) $sentCount++;
                else $errorCount++;
            }
        }

        $this->info("✅ Selesai! Terkirim: {$sentCount}, Gagal: {$errorCount}");
        Log::info("Loan reminders sent: {$sentCount} success, {$errorCount} errors");

        return Command::SUCCESS;
    }

    private function sendReminder(LoanPayment $payment, string $type, string $coopName, string $signature): bool
    {
        $loan = $payment->loan;
        $member = $loan->member ?? null;
        $user = $member->user ?? null;

        if (!$user || !$user->email) {
            $this->warn("⏭ Skip: Member tanpa email (Loan #{$loan->loan_number})");
            return false;
        }

        $memberName = $user->name;
        $email = $user->email;
        $dueDate = $payment->due_date->format('d F Y');
        $amount = number_format($payment->amount, 0, ',', '.');
        $installmentNo = $payment->installment_number ?? '-';
        $loanNumber = $loan->loan_number;
        $remaining = number_format($loan->remaining_amount, 0, ',', '.');

        if ($type === 'overdue') {
            $daysLate = now()->diffInDays($payment->due_date);
            $subject = "⚠️ Cicilan Terlambat {$daysLate} Hari - {$coopName}";
            $body = "Yth. Bapak/Ibu {$memberName},\n\n";
            $body .= "Dengan hormat,\n\n";
            $body .= "Kami menginformasikan bahwa cicilan pinjaman Anda telah MELEWATI jatuh tempo.\n\n";
            $body .= "Detail Cicilan:\n";
            $body .= "━━━━━━━━━━━━━━━━━━━━━\n";
            $body .= "No. Pinjaman   : {$loanNumber}\n";
            $body .= "Cicilan ke-    : {$installmentNo}\n";
            $body .= "Jatuh Tempo    : {$dueDate}\n";
            $body .= "Keterlambatan  : {$daysLate} hari\n";
            $body .= "Jumlah Cicilan : Rp {$amount}\n";
            $body .= "Sisa Pinjaman  : Rp {$remaining}\n";
            $body .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
            $body .= "Mohon segera melakukan pembayaran cicilan Anda.\n";
            $body .= "Keterlambatan pembayaran dapat dikenakan denda sesuai ketentuan yang berlaku.\n\n";
            $body .= "Apabila sudah melakukan pembayaran, mohon abaikan pemberitahuan ini.\n";
            $body .= "Terima kasih atas perhatian dan kerjasamanya.";
        } else {
            $daysUntil = now()->diffInDays($payment->due_date);
            $subject = "📋 Pengingat Cicilan ({$daysUntil} hari lagi) - {$coopName}";
            $body = "Yth. Bapak/Ibu {$memberName},\n\n";
            $body .= "Dengan hormat,\n\n";
            $body .= "Kami mengingatkan bahwa cicilan pinjaman Anda akan segera jatuh tempo.\n\n";
            $body .= "Detail Cicilan:\n";
            $body .= "━━━━━━━━━━━━━━━━━━━━━\n";
            $body .= "No. Pinjaman   : {$loanNumber}\n";
            $body .= "Cicilan ke-    : {$installmentNo}\n";
            $body .= "Jatuh Tempo    : {$dueDate}\n";
            $body .= "Jumlah Cicilan : Rp {$amount}\n";
            $body .= "Sisa Pinjaman  : Rp {$remaining}\n";
            $body .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
            $body .= "Mohon untuk melakukan pembayaran cicilan sebelum tanggal jatuh tempo.\n\n";
            $body .= "Apabila sudah melakukan pembayaran, mohon abaikan pemberitahuan ini.\n";
            $body .= "Terima kasih atas perhatian dan kerjasamanya.";
        }

        $body .= $signature;

        try {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });

            $this->info("📧 Terkirim: {$memberName} ({$email}) - {$type}");
            return true;

        } catch (\Exception $e) {
            $this->error("❌ Gagal: {$email} - " . $e->getMessage());
            Log::error("Loan reminder failed for {$email}: " . $e->getMessage());
            return false;
        }
    }
}
