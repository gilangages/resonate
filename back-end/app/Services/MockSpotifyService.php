<?php

namespace App\Services;

use App\Contracts\MusicProvider;

class MockSpotifyService implements MusicProvider
{
    public function searchTrack(string $query): array
    {
        return [
            'tracks' => [
                'items' => [
                    [
                        'id' => 'mock_001',
                        'name' => 'Mock Song One',
                        'artists' => [
                            ['name' => 'Mock Artist'],
                        ],
                        'preview_url' => null,
                        'album' => [
                            'images' => [
                                ['url' => 'https://placehold.co/300x300'],
                            ],
                        ],
                    ],
                    [
                        'id' => 'mock_002',
                        'name' => 'Mock Song Two',
                        'artists' => [
                            ['name' => 'Another Artist'],
                        ],
                        'preview_url' => null,
                        'album' => [
                            'images' => [
                                ['url' => 'https://placehold.co/300x300'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
