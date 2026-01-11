<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;

    public function __construct($user, $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Atur Ulang Password Music Note')
            ->html("
                        <div style='font-family: sans-serif; padding: 20px; background-color: #f4f4f4;'>
                            <div style='max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px;'>
                                <h1 style='color: #9a203e;'>Halo, {$this->user->name}!</h1>
                                <p>Kami menerima permintaan untuk mengatur ulang password akun Anda.</p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='{$this->resetUrl}'
                                       style='background-color: #9a203e; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                                       Atur Ulang Password
                                    </a>
                                </div>
                                <p>Link ini akan kedaluwarsa dalam 60 menit. Jika Anda tidak merasa meminta ini, abaikan email ini.</p>
                                <hr style='border: none; border-top: 1px solid #eee;'>
                                <p style='font-size: 12px; color: #888;'>Team Music Note</p>
                            </div>
                        </div>
                    ");
    }
}
