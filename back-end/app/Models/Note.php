<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
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

    // Relasi ke Induk (Note yang dibalas)
    public function parent()
    {
        return $this->belongsTo(Note::class, 'parent_id');
    }

    // Relasi ke Anak (Balasan-balasan)
    public function replies()
    {
        return $this->hasMany(Note::class, 'parent_id')->orderBy('created_at', 'desc');
    }
}
