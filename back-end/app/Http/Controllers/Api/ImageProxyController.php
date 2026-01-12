<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['error' => 'URL required'], 400);
        }

        // Ambil path relatif dari URL storage
        $baseUrl = url('/storage');
        $relativePath = str_replace($baseUrl, '', $url);
        $relativePath = ltrim($relativePath, '/');

        // Pastikan menggunakan disk 'public' sesuai config filesystems.php Anda
        if (Storage::disk('public')->exists($relativePath)) {
            $path = Storage::disk('public')->path($relativePath);
            $mimeType = Storage::disk('public')->mimeType($relativePath);

            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
                'Access-Control-Allow-Origin' => '*', // Penting untuk library capture di front-end
                'ETag' => md5($relativePath . filemtime($path)), // Membedakan tiap file secara unik
            ]);
        }

        return response()->json(['error' => 'Image not found: ' . $relativePath], 404);
    }
}
