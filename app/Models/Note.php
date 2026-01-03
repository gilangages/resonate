<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'music_track_id',
        'is_anonymous',
    ];

    // Note milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
