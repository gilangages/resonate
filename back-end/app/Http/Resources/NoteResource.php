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
            // UBAH KE 'music_' DAN BACA DARI PROPERTI MODEL YANG BENAR
            'music_track_id' => $this->music_track_id,
            'music_track_name' => $this->music_track_name,
            'music_artist_name' => $this->music_artist_name, // di DB namanya music_artist_name
            'music_album_image' => $this->music_album_image,
            'music_preview_url' => $this->music_preview_url,
            'music_track_link' => $this->music_track_link,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
