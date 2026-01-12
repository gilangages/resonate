<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserAppealNotification extends Notification
{
    use Queueable;

    protected $userEmail;
    protected $reason;

    public function __construct($userEmail, $reason)
    {
        $this->userEmail = $userEmail;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        // Simpan ke database agar muncul di lonceng notifikasi aplikasi
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Banding Akun: {$this->userEmail}",
            'description' => "Alasan: {$this->reason}",
            'type' => 'appeal', // Tanda bahwa ini notifikasi banding
            'email' => $this->userEmail, // Admin bisa cari user by email nanti
        ];
    }
}
