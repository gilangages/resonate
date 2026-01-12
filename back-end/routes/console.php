<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PRUNING OTOMATIS: Hapus notifikasi lama agar database tidak bengkak
Schedule::call(function () {
    // 1. Hapus notifikasi yang sudah dibaca DAN lebih tua dari 30 hari
    $deletedRead = DB::table('notifications')
        ->whereNotNull('read_at')
        ->where('created_at', '<', now()->subDays(30))
        ->delete();

    // 2. Hapus notifikasi (meski belum dibaca) jika sudah lebih dari 3 bulan (junk)
    $deletedUnread = DB::table('notifications')
        ->whereNull('read_at')
        ->where('created_at', '<', now()->subMonths(3))
        ->delete();

    // Log info (opsional, bisa dilihat di laravel.log)
    // \Illuminate\Support\Facades\Log::info("Notification Pruning: Deleted $deletedRead read & $deletedUnread unread notifications.");

})->daily(); // Jalankan setiap tengah malam (00:00)
