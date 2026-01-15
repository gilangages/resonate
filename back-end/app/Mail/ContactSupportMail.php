<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan/Pesan Baru dari Resonate: ' . $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Kita gunakan view simple text/markdown agar tidak perlu buat file blade baru yg rumit
        return new Content(
            htmlString: '
                <h3>Pesan Baru dari Pengguna Resonate</h3>
                <p><strong>Nama:</strong> ' . $this->data['name'] . '</p>
                <p><strong>Email Pengirim:</strong> ' . $this->data['email'] . '</p>
                <p><strong>Subject:</strong> ' . $this->data['subject'] . '</p>
                <hr>
                <p><strong>Pesan:</strong></p>
                <p style="white-space: pre-wrap;">' . $this->data['message'] . '</p>
            '
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
