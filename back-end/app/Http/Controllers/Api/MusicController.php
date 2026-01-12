<?php

namespace App\Http\Controllers\Api;

use App\Contracts\MusicProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MusicController extends Controller
{
    public function search(Request $request, MusicProvider $music)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        return response()->json(
            $music->searchTrack($request->q)
        );
    }
}
