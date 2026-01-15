<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AudioStreamController extends Controller
{
    public function stream($trackId)
    {
        // 1. Cek Cache dulu (Key: audio_url_12345)
        // Kita simpan selama 30 menit (1800 detik) biar ga nembak API Deezer terus
        $previewUrl = Cache::remember("audio_url_{$trackId}", 1800, function () use ($trackId) {

            // 2. Kalau ga ada di cache, tembak API Deezer
            $response = Http::get("https://api.deezer.com/track/{$trackId}");

            if ($response->successful()) {
                return $response->json()['preview'] ?? null;
            }

            return null;
        });

        // 3. Jika URL ditemukan, Redirect browser ke sana
        if ($previewUrl) {
            return redirect($previewUrl);
        }

        // 4. Jika gagal, return 404 (atau file mp3 error default)
        return response()->json(['message' => 'Audio not found'], 404);
    }
}
