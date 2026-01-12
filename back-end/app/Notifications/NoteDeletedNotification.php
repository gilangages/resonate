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
        $this->noteContentSnippet = substr($noteContent, 0, 30) . '...'; // Pendek saja
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            // Title singkat untuk header notif
            'title' => 'Catatan Dihapus Admin',

            // Message fokus ke "Apa yang terjadi"
            'message' => "Pesanmu: \"{$this->noteContentSnippet}\" telah dihapus.",

            // Reason dikirim terpisah agar frontend bisa styling khusus (misal warna merah)
            'reason' => $this->reason,

            // Tipe ini kunci untuk membedakan icon di frontend
            'type' => 'alert',

            'time' => now(),
        ];
    }
}
