<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApprovedNotification extends Notification
{
    use Queueable;

    protected $loan;

    /**
     * Create a new notification instance.
     */
    public function __construct($loan)
    {
        $this->loan = $loan;
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
        $amount = number_format($this->loan->amount, 0, ',', '.');
        
        return (new MailMessage)
            ->subject('Pengajuan Pinjaman Disetujui')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Kami informasikan bahwa pengajuan pinjaman Anda telah **DISETUJUI**.')
            ->line('**Detail Pinjaman:**')
            ->line('• Jumlah: Rp ' . $amount)
            ->line('• Tenor: ' . $this->loan->duration_months . ' bulan')
            ->line('• Bunga: ' . $this->loan->interest_rate . '% per tahun')
            ->action('Lihat Detail Pinjaman', route('loans.show', $this->loan->id))
            ->line('Silakan tunggu proses pencairan dana oleh admin.')
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'loan_id' => $this->loan->id,
            'amount' => $this->loan->amount,
            'message' => 'Pengajuan pinjaman Rp ' . number_format($this->loan->amount, 0, ',', '.') . ' telah disetujui.',
            'type' => 'loan_approved'
        ];
    }
}
