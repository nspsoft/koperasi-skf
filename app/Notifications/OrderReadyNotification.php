<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyNotification extends Notification
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan Anda Siap Diambil!')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Pesanan Anda dengan nomor invoice **' . $this->transaction->invoice_number . '** sudah siap!')
            ->line('Silakan ambil pesanan Anda di toko Koperasi.')
            ->action('Lacak Pesanan', route('shop.track', $this->transaction->id))
            ->line('Terima kasih telah berbelanja di Koperasi Mart!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'message' => 'Pesanan ' . $this->transaction->invoice_number . ' sudah siap diambil!',
            'type' => 'order_ready',
        ];
    }
}
