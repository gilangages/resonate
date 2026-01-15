<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_replies', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel notes (induk)
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            // Relasi ke user yang membalas
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('content')->nullable();
            $table->string('initial_name')->nullable(); // Nama samaran pengirim

            // Kolom Musik (Disalin dari struktur notes)
            $table->string('music_track_id')->nullable();
            $table->text('music_track_name')->nullable();
            $table->text('music_artist_name')->nullable();
            $table->text('music_album_image')->nullable();
            $table->text('music_preview_url')->nullable();
            $table->text('music_track_link')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_replies');
    }
};
