<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth sudah di middleware
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'recipient' => 'nullable|string|max:50',
            'spotify_track_id' => 'nullable|string|max:255',
            'spotify_track_name' => 'nullable|string|max:255',
            'spotify_artist' => 'nullable|string|max:255',
            'spotify_album_image' => 'nullable|url',
            'spotify_preview_url' => 'nullable|url',
            'is_anonymous' => 'boolean',
        ];
    }
}
