<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan note ini milik user yang sedang login
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Data Pesan
            'content' => 'required|string',
            'recipient' => 'required|string|max:255',
            'initial_name' => 'nullable|string|max:255', // Pengganti is_anonymous

            // Data Lagu (WAJIB DITAMBAHKAN AGAR BISA DIUPDATE)
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
            'content.required' => 'Isi pesan tidak boleh kosong.',
            'recipient.required' => 'Nama penerima wajib diisi.',
            'music_track_id.required' => 'Lagu wajib dipilih.',
        ];
    }
}
