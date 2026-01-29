<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOnlineOrderNotification extends Notification
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
        $amount = number_format($this->transaction->total_amount, 0, ',', '.');
        
        return (new MailMessage)
            ->subject('Pesanan Online Baru: ' . $this->transaction->invoice_number)
            ->greeting('Halo Admin!')
            ->line('Ada pesanan online baru yang masuk.')
            ->line('**Detail Pesanan:**')
            ->line('• Invoice: ' . $this->transaction->invoice_number)
            ->line('• Total: Rp ' . $amount)
            ->action('Lihat Pesanan', route('pos.manage', $this->transaction->id))
            ->line('Segera proses pesanan ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $memberName = $this->transaction->user ? $this->transaction->user->name : 'Guest';
        
        return [
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'total_amount' => $this->transaction->total_amount,
            'member_name' => $memberName,
            'message' => 'Pesanan baru dari ' . $memberName . ' senilai Rp ' . number_format($this->transaction->total_amount, 0, ',', '.'),
            'type' => 'new_online_order',
        ];
    }
}
