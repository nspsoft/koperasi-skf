<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberActivatedNotification extends Notification
{
    use Queueable;

    protected $member;

    public function __construct($member)
    {
        $this->member = $member;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akun Anda Telah Aktif')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Akun Anda sudah aktif dan siap digunakan.')
            ->line('ID Anggota Anda: ' . ($this->member->member_id ?? '-'))
            ->action('Masuk ke Akun', route('login'))
            ->line('Terima kasih telah mendaftar di Koperasi SKF.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'member_id' => $this->member->id ?? null,
            'message' => 'Akun Anda telah aktif. ID Anggota: ' . ($this->member->member_id ?? '-'),
            'type' => 'member_activated',
        ];
    }
}

