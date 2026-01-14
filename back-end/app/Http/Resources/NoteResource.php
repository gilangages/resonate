<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $displayName = $this->initial_name ?? $this->user->name;

        return [
            'id' => $this->id,
            'content' => $this->content,
            'recipient' => $this->recipient,
            'initial_name' => $this->initial_name,
            'author' => $displayName,
            'author_avatar' => $this->user->avatar
            ? url('storage/' . $this->user->avatar)
            : $this->user->photo_url,

            // [FIX] Tambahkan field ini agar sesuai docs & kebutuhan frontend
            'spotify_track_id' => $this->spotify_track_id,
            'spotify_track_name' => $this->spotify_track_name, // Pastikan kolom ini ada di DB
            'spotify_artist' => $this->spotify_artist, // Pastikan kolom ini ada di DB
            'spotify_album_image' => $this->spotify_album_image, // Pastikan kolom ini ada di DB
            'spotify_preview_url' => $this->spotify_preview_url, // Pastikan kolom ini ada di DB

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
