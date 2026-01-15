<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->renameColumn('spotify_track_id', 'music_track_id');
            $table->renameColumn('spotify_track_name', 'music_track_name');
            $table->renameColumn('spotify_artist', 'music_artist_name');
            $table->renameColumn('spotify_album_image', 'music_album_image');
            $table->renameColumn('spotify_preview_url', 'music_preview_url');
            $table->renameColumn('spotify_track_link', 'music_track_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk rollback
        Schema::table('notes', function (Blueprint $table) {
            $table->renameColumn('music_track_id', 'spotify_track_id');
            $table->renameColumn('music_track_name', 'spotify_track_name');
            $table->renameColumn('music_artist_name', 'spotify_artist');
            $table->renameColumn('music_album_image', 'spotify_album_image');
            $table->renameColumn('music_preview_url', 'spotify_preview_url');
            $table->renameColumn('music_track_link', 'spotify_track_link');
        });
    }
};
