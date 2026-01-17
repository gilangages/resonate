<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AudioStreamController extends Controller
{
    public function stream($trackId)
    {
        // 1. Ambil URL Segar dari Deezer (Jangan dicache lama-lama, 5 menit cukup)
        // Kita cache API response-nya saja, bukan URL fisiknya, untuk menghindari rate limit Deezer
        $deezerUrl = Cache::remember("deezer_track_{$trackId}", 300, function () use ($trackId) {
            try {
                $response = Http::get("https://api.deezer.com/track/{$trackId}");
                if ($response->successful()) {
                    return $response->json()['preview'] ?? null;
                }
            } catch (\Exception $e) {
                return null;
            }
            return null;
        });

        if (!$deezerUrl) {
            return response()->json(['message' => 'Audio not found'], 404);
        }

        // 2. Teknik Streaming Proxy (Agar HP Bisa Putar)
        // Ini memaksa semua traffic lewat HTTPS Render dan mendukung "Range Request"

        $range = request()->header('Range');
        $start = 0;
        $end = null;

        // Cek ukuran file asli ke Deezer (Tanpa download, cuma tanya header)
        try {
            $head = Http::head($deezerUrl);
            $fileSize = $head->header('Content-Length');

            // Fallback jika Deezer menolak HEAD request
            if (!$fileSize) {
                $fileSize = 1000000;
            }
            // Asumsi 1MB (preview biasanya kecil)

            $mimeType = $head->header('Content-Type') ?? 'audio/mpeg';
        } catch (\Exception $e) {
            // Kalau gagal cek head, redirect aja sebagai fallback terakhir
            return redirect($deezerUrl);
        }

        $end = $fileSize - 1;

        // Logika HTTP 206 Partial Content (Wajib buat iPhone/Android)
        if ($range) {
            list(, $range) = explode('=', $range, 2);
            $range = explode('-', $range);
            $start = intval($range[0]);
            if (isset($range[1]) && is_numeric($range[1])) {
                $end = intval($range[1]);
            }
        }

        $length = $end - $start + 1;
        $status = 206; // Partial Content

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Accept-Ranges' => 'bytes',
            'Content-Range' => "bytes $start-$end/$fileSize",
            'Cache-Control' => 'no-cache, no-store, must-revalidate', // Hindari browser cache file rusak
        ];

        // 3. Streaming Data
        return response()->stream(function () use ($deezerUrl, $start, $end) {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "Range: bytes=$start-$end\r\n",
                ],
            ]);

            $stream = @fopen($deezerUrl, 'rb', false, $context);

            if ($stream) {
                while (!feof($stream)) {
                    echo fread($stream, 8192); // Kirim per 8KB
                    flush();
                }
                fclose($stream);
            }
        }, $status, $headers);
    }
}
