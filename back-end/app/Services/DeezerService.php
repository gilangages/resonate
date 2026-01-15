<?php

namespace App\Services;

use App\Contracts\MusicProvider;
use Illuminate\Support\Facades\Http;

class DeezerService implements MusicProvider
{
    public function searchTrack(string $query): array
    {
        $response = Http::get('https://api.deezer.com/search', [
            'q' => $query,
            'limit' => 10,
        ]);

        if ($response->failed()) {
            // Return struktur kosong yang sesuai format lama
            return ['tracks' => ['items' => []]];
        }

        $data = $response->json();

        $mappedTracks = collect($data['data'] ?? [])->map(function ($track) {
            return [
                'id' => (string) $track['id'],
                'name' => $track['title'],
                // UBAH 1: Format Artist disamakan jadi array agar frontend tidak error
                'artists' => [
                    ['name' => $track['artist']['name']],
                ],
                // UBAH 2: Format Image disamakan (masuk ke album->images->url)
                'album' => [
                    'images' => [
                        ['url' => $track['album']['cover_medium'] ?? ''],
                    ],
                ],
                'preview_url' => $track['preview'],
                // UBAH 3: External URL disamakan key-nya
                'external_urls' => [
                    'spotify' => $track['link'], // Kita pinjam key 'spotify' isinya link deezer
                ],
            ];
        })->toArray();

        // UBAH 4: Return dengan wrapper 'tracks' -> 'items'
        return [
            'tracks' => [
                'items' => $mappedTracks,
            ],
        ];
    }
}
