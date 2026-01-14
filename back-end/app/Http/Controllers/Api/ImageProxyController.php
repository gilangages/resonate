<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage; // Tambahkan ini jika ingin proxy URL eksternal
use Symfony\Component\HttpFoundation\Response;

class ImageProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['error' => 'URL required'], 400);
        }

        try {
            // 1. Logika untuk file lokal di storage
            $baseUrl = url('/storage');

            if (str_contains($url, $baseUrl)) {
                $relativePath = str_replace($baseUrl, '', $url);
                $relativePath = ltrim($relativePath, '/');

                if (Storage::disk('public')->exists($relativePath)) {
                    $path = Storage::disk('public')->path($relativePath);
                    $mimeType = Storage::disk('public')->mimeType($relativePath);

                    return response()->file($path, [
                        'Content-Type' => $mimeType,
                        'Cache-Control' => 'public, max-age=86400', // Cache 1 hari agar cepat
                        'Access-Control-Allow-Origin' => '*',
                        'ETag' => md5($relativePath . filemtime($path)),
                    ]);
                }
            }

            // 2. Logika jika URL adalah link eksternal (misal dari S3 atau API lain)
            $response = Http::withOptions(['verify' => false])->get($url);

            if ($response->successful()) {
                return response($response->body(), 200, [
                    'Content-Type' => $response->header('Content-Type'),
                    'Access-Control-Allow-Origin' => '*',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }

            return response()->json(['error' => 'Image not found'], 404);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
