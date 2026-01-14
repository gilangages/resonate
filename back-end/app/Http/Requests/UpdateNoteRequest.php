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
            'spotify_track_id' => 'required|string',
            'spotify_track_name' => 'required|string',
            'spotify_artist' => 'required|string',
            'spotify_album_image' => 'nullable|string',
            'spotify_preview_url' => 'nullable|string',
        ];
    }
}
