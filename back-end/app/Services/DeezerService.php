<?php

namespace App\Services;

use App\Contracts\MusicProvider;
use Illuminate\Support\Facades\Http;

class DeezerService implements MusicProvider
{
    public function searchTrack(string $query): array
    {
        // Deezer Public API Endpoint
        $response = Http::get('https://api.deezer.com/search', [
            'q' => $query,
            'limit' => 10,
        ]);

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        // Mapping respons Deezer ke format standar aplikasi kita
        // Struktur array ini harus konsisten digunakan di Frontend nantinya
        return collect($data['data'] ?? [])->map(function ($track) {
            return [
                'id' => $track['id'],
                'name' => $track['title'],
                'artist' => $track['artist']['name'],
                'image' => $track['album']['cover_medium'] ?? null, // cover_medium ukuran 250x250
                'preview_url' => $track['preview'], // mp3 30 detik
                'external_url' => $track['link'], // Link ke deezer
                'provider' => 'deezer', // penanda opsional
            ];
        })->toArray();
    }
}
