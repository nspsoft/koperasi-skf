<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengumuman Baru: ' . $this->announcement->title)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Ada pengumuman baru dari Koperasi:')
            ->line('**' . $this->announcement->title . '**')
            ->line(\Illuminate\Support\Str::limit(strip_tags($this->announcement->content), 150))
            ->action('Baca Selengkapnya', route('announcements.index'))
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'message' => 'Pengumuman baru: ' . $this->announcement->title,
            'type' => 'new_announcement',
        ];
    }
}
