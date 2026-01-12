<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NoteDeletedNotification extends Notification
{
    use Queueable;

    private $noteContentSnippet;
    private $reason;

    public function __construct($noteContent, $reason = 'Konten mengandung unsur negatif.')
    {
        // Simpan potongan konten agar user ingat note mana yg dihapus
        $this->noteContentSnippet = substr($noteContent, 0, 50) . '...';
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke database
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Catatan Dihapus Admin',
            'message' => "Pesanmu: \"{$this->noteContentSnippet}\" telah dihapus oleh admin. Alasan: {$this->reason}",
            'reason' => $this->reason,
            'type' => 'alert', // Bisa dipakai untuk styling di frontend
            'time' => now(),
        ];
    }
}
