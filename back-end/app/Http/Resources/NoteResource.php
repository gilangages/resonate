<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // LOGIC UTAMA:
        // Jika 'initial_name' diisi user, pakai itu.
        // Jika 'initial_name' null, ambil nama asli dari relasi user.
        $displayName = $this->initial_name ?? $this->user->name;

        return [
            'id' => $this->id,
            'content' => $this->content,
            'recipient' => $this->recipient,

            // Kita kirim keduanya biar frontend tau
            'initial_name' => $this->initial_name,

            // Inilah yang ditampilkan sebagai PENULIS di layar
            'author' => $displayName,

            // Avatar menyesuaikan inisial nama yang tampil
            'author_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=random',

            'spotify_track_id' => $this->spotify_track_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
