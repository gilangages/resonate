<?php

namespace App\Http\Controllers\Api;

use App\Contracts\MusicProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MusicController extends Controller
{
    public function search(Request $request, MusicProvider $music)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);
        $query = $request->q;

        // Cek apakah pencarian ini sudah ada di cache? (Simpan selama 1 jam / 3600 detik)
        $result = Cache::remember("search_music_{$query}", 3600, function () use ($music, $query) {
            return $music->searchTrack($query);
        });

        return response()->json($result);
    }
}
