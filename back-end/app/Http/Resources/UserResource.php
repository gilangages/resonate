<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $avatar = $this->avatar;

        // Jika ada avatar, cek apakah itu URL lengkap (Google) atau file lokal
        if ($avatar) {
            if (!filter_var($avatar, FILTER_VALIDATE_URL)) {
                // Jika bukan URL, berarti file lokal di storage
                $avatar = url('storage/' . $avatar);
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'has_password' => !is_null($this->password),
            'avatar' => $avatar, // Sekarang sudah fleksibel
            'photo_url' => $this->photo_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
