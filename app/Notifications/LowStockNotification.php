<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $product;

    /**
     * Create a new notification instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
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
            ->subject('Peringatan Stok Rendah: ' . $this->product->name)
            ->greeting('Halo Admin!')
            ->line('Stok produk berikut sudah mencapai batas minimum:')
            ->line('**' . $this->product->name . '**')
            ->line('Stok saat ini: ' . $this->product->stock . ' unit')
            ->line('Batas minimum: ' . $this->product->min_stock . ' unit')
            ->action('Kelola Stok', route('inventory.low-stock'))
            ->line('Segera lakukan restock untuk memastikan ketersediaan produk.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'stock' => $this->product->stock,
            'min_stock' => $this->product->min_stock,
            'message' => 'Stok ' . $this->product->name . ' tinggal ' . $this->product->stock . ' unit.',
            'type' => 'low_stock',
        ];
    }
}
