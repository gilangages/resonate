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
            $table->string('spotify_track_name')->nullable();
            $table->string('spotify_artist')->nullable();
            $table->string('spotify_album_image')->nullable();
            $table->string('spotify_preview_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_track_name',
                'spotify_artist',
                'spotify_album_image',
                'spotify_preview_url',
            ]);
        });
    }
};
