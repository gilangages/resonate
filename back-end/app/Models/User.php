<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Note;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * @use HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'card_theme',
        'password',
        'avatar',
        'google_id',
        'role', // <--- Tambahkan ini
        'is_banned', // <--- TAMBAHKAN INI
        'ban_reason', // <--- TAMBAHKAN INI
    ];

    /**
     * Tambahkan 'photo_url' ke dalam JSON response
     * Jadi saat frontend request user, field ini otomatis ada.
     */
    protected $appends = ['photo_url'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Membuat Virtual Column: photo_url
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Kita ambil nama user, misal "Abdi An"
                $name = urlencode($this->name);

                // Warna Tema Resonate (Maroon & Pinkish White)
                $background = '9A203E';
                $color = 'FFE4E6';

                // Style: "notionists" (mirip gambar sketch Notion, cocok banget buat app Catatan!)
                return "https://api.dicebear.com/9.x/notionists/svg?seed={$name}&backgroundColor=9a203e";
            },
        );
    }

    //user punya banyak notes
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
