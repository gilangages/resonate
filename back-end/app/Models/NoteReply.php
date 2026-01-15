<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'user_id',
        'content',
        'initial_name',
        'music_track_id',
        'music_track_name',
        'music_artist_name',
        'music_album_image',
        'music_preview_url',
        'music_track_link',
    ];

    // Pemilik balasan
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Terhubung ke Note induk
    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}
