<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Ubah tipe data jadi TEXT agar muat URL panjang Deezer
            $table->text('music_preview_url')->nullable()->change();
            $table->text('music_album_image')->nullable()->change();
            $table->text('music_track_link')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Kembalikan ke STRING (varchar 255) jika rollback
            $table->string('music_preview_url')->nullable()->change();
            $table->string('music_album_image')->nullable()->change();
            $table->string('music_track_link')->nullable()->change();
        });
    }
};
