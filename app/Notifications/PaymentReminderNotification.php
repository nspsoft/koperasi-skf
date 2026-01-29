<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    protected $loanPayment;

    /**
     * Create a new notification instance.
     */
    public function __construct($loanPayment)
    {
        $this->loanPayment = $loanPayment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->loanPayment->amount, 0, ',', '.');
        $dueDate = \Carbon\Carbon::parse($this->loanPayment->due_date)->format('d F Y');
        
        return (new MailMessage)
            ->subject('Pengingat Pembayaran Angsuran')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Ini adalah pengingat untuk pembayaran angsuran pinjaman Anda.')
            ->line('**Detail Pembayaran:**')
            ->line('• Angsuran ke: ' . $this->loanPayment->installment_number)
            ->line('• Jumlah: Rp ' . $amount)
            ->line('• Jatuh Tempo: ' . $dueDate)
            ->action('Lihat Detail Pinjaman', route('loans.show', $this->loanPayment->loan_id))
            ->line('Mohon lakukan pembayaran sebelum tanggal jatuh tempo.')
            ->line('Terima kasih atas kerjasamanya!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'loan_payment_id' => $this->loanPayment->id,
            'amount' => $this->loanPayment->amount,
            'due_date' => $this->loanPayment->due_date,
            'message' => 'Angsuran Rp ' . number_format($this->loanPayment->amount, 0, ',', '.') . ' jatuh tempo segera.',
            'type' => 'payment_reminder'
        ];
    }
}
