<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditBillNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $details;

    /**
     * Create a new notification instance.
     *
     * @param array $details ['total_debt' => float, 'invoice_count' => int]
     */
    public function __construct($details)
    {
        $this->details = $details;
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
        return (new MailMessage)
            ->subject('🔔 Tagihan Kredit Mart - Koperasi SKF')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Berikut adalah rincian tagihan Kredit Mart Anda yang belum lunas:')
            ->line('')
            ->line('**Total Tagihan: Rp ' . number_format($this->details['total_debt'], 0, ',', '.') . '**')
            ->line('Jumlah Transaksi: ' . $this->details['invoice_count'] . ' Invoice')
            ->line('')
            ->line('Mohon untuk segera melakukan pembayaran atau pelunasan.')
            ->action('Lihat Detail Pembelian', route('shop.history'))
            ->line('Jika ada pertanyaan, silakan hubungi pengurus koperasi.')
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Tagihan Kredit Mart',
            'message' => 'Total Tagihan Anda: Rp ' . number_format($this->details['total_debt'], 0, ',', '.'),
            'type' => 'bill',
            'amount' => $this->details['total_debt']
        ];
    }
}
