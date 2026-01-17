<?php

namespace App\Http\Requests;

use App\Rules\NoBadWords;
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
            'content' => ['required|string|max:1000', new NoBadWords()],

            // 2. RECIPIENT (Kepada): Wajib (Sesuai UI kamu)
            'recipient' => 'required|string|max:50',

            // 3. INITIAL NAME (Kirim Sebagai):
            // Tetap 'nullable' karena logic-nya:
            // Jika user pilih "Nama Asli", frontend mungkin kirim null/kosong.
            // Jika user ketik "Nama Samaran", frontend kirim string.
            'initial_name' => 'nullable|string|max:50',

            'music_track_id' => 'required|string|max:255',
            'music_track_name' => 'required|string|max:255',
            'music_artist_name' => 'required|string|max:255', // sesuaikan nama key
            'music_album_image' => 'required|url',
            'music_preview_url' => 'nullable|url',
            'music_track_link' => ['nullable', 'string', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Isi pesan wajib diisi.',
            'content.max' => 'Pesan tidak boleh lebih dari 1000 karakter.',
            'recipient.required' => 'Nama penerima (Kepada) wajib diisi.',
            'recipient.max' => 'Nama penerima maksimal 50 karakter.',
            'initial_name.max' => 'Nama samaran maksimal 50 karakter.',

            // Pesan Error Khusus Lagu (Biar user paham)
            'music_track_id.required' => 'Kamu belum memilih lagu. Silakan cari dan pilih lagu.',
            'music_album_image.required' => 'Data lagu tidak lengkap (gambar album hilang).',
        ];
    }
}
