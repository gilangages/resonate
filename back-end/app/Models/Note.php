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
        'spotify_track_id',
        'spotify_track_name',
        'spotify_artist',
        'spotify_album_image',
        'spotify_preview_url',
        'spotify_track_link',
    ];

    // Note milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
