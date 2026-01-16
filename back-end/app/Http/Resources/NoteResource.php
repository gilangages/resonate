<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Ambil user pembuat note ini
        $user = $this->user;

        // 2. Ambil avatar dari USER, bukan dari $this (note)
        $avatar = $user ? $user->avatar : null;

        // 3. Logic pengecekan URL (untuk Google vs Lokal)
        if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL)) {
            $avatar = url('storage/' . $avatar);
        }

        return [
            'id' => $this->id,
            'content' => $this->content,
            'recipient' => $this->recipient,
            'initial_name' => $this->initial_name,

            // Gunakan initial_name jika ada, jika tidak ada pakai nama asli user
            'author_name' => $this->initial_name ?? ($user ? $user->name : 'Anonymous'),

            // SEKARANG INI AKAN BERISI URL GOOGLE ATAU URL STORAGE LOKAL
            'author_avatar' => $avatar,

            // Tambahkan juga photo_url (Dicebear) sebagai cadangan di frontend
            'author_photo_url' => $user ? $user->photo_url : null,

            'music_track_id' => $this->music_track_id,
            'music_track_name' => $this->music_track_name,
            'music_artist_name' => $this->music_artist_name,
            'music_album_image' => $this->music_album_image,
            'music_preview_url' => $this->music_preview_url,
            'music_track_link' => $this->music_track_link,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
