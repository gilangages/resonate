<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserAppealNotification extends Notification implements ShouldQueue// Tambah ShouldQueue biar ringan

{
    use Queueable;

    public $email;
    public $message; // <--- 1. Tambahkan properti ini

    /**
     * Create a new notification instance.
     */
    public function __construct($email, $message) // <--- 2. Terima message di constructor
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Bisa tambah 'mail' jika perlu
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Permintaan Banding Akun',
            'message' => "User {$this->email} mengajukan banding atas pemblokiran akun.",
            'email' => $this->email,
            'appeal_message' => $this->message, // <--- 3. Masukkan ke data JSON
            'type' => 'appeal', // Identitas notifikasi
            'created_at' => now(),
        ];
    }
}
