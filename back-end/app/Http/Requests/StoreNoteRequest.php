<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 1. CONTENT (Pesan): Wajib
            'content' => 'required|string|max:1000',

            // 2. RECIPIENT (Kepada): Wajib (Sesuai UI kamu)
            'recipient' => 'required|string|max:50',

            // 3. INITIAL NAME (Kirim Sebagai):
            // Tetap 'nullable' karena logic-nya:
            // Jika user pilih "Nama Asli", frontend mungkin kirim null/kosong.
            // Jika user ketik "Nama Samaran", frontend kirim string.
            'initial_name' => 'nullable|string|max:50',

            // 4. SPOTIFY DATA (Lagu):
            // Kita ubah jadi REQUIRED karena user WAJIB pilih lagu.
            'spotify_track_id' => 'required|string|max:255',

            // Metadata juga wajib required supaya DB tidak bolong
            'spotify_track_name' => 'required|string|max:255',
            'spotify_artist' => 'required|string|max:255',
            'spotify_album_image' => 'required|url',

            // Preview url kadang dari Spotify-nya kosong, jadi aman kalau nullable
            'spotify_preview_url' => 'nullable|url',
            'spotify_track_link' => ['nullable', 'string', 'url'],
        ];
    }
}
