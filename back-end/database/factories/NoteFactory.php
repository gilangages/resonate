<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // Relasi ke User (otomatis buat user baru jika tidak diisi)
            'user_id' => User::factory(),

            // Data Pesan
            'content' => $this->faker->sentence(10), // Contoh: "Lorem ipsum dolor sit amet..."
            'recipient' => $this->faker->name(),
            'initial_name' => $this->faker->firstName(),

            // Data Musik Dummy
            'music_track_id' => (string) $this->faker->numberBetween(10000, 99999),
            'music_track_name' => $this->faker->catchPhrase(), // Judul lagu random
            'music_artist_name' => $this->faker->name(),
            'music_album_image' => 'https://via.placeholder.com/300',
            'music_preview_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'music_track_link' => 'https://open.spotify.com/track/123456',

            // Timestamp
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
