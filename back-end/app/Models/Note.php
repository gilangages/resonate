<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', // pastikan ini sesuai nama kolom di DB
        'recipient',
        'initial_name',
        'music_track_id', // sebelumnya spotify_track_id
        'music_track_name', // sebelumnya spotify_track_name
        'music_artist_name', // sebelumnya spotify_artist
        'music_album_image', // sebelumnya spotify_album_image
        'music_preview_url', // sebelumnya spotify_preview_url
        'music_track_link', // sebelumnya spotify_track_link
        'google_id',
    ];

    // Note milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
