<?php

use App\Http\Controllers\Api\AudioStreamController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/test', function () {
    return response()->json(['status' => 'API works!']);
});

Route::get('/test-sanctum', function () {
    return 'Sanctum OK';
});

// Route Public (Register/Login)
Route::post('/users', RegisterController::class);
Route::post('/users/login', LoginController::class);

//get  Global Notes (Bisa diakses siapa saja)
Route::get('/notes/global', [NoteController::class, 'globalIndex']);
Route::get('/stream/{trackId}', [AudioStreamController::class, 'stream']);

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// 🔐 AUTH ROUTES (Prioritas Tinggi)
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/users/logout', LogoutController::class);

    // ✅ POSISI 'my' AMAN DI SINI
    // Karena route {id} belum didefinisikan di atasnya
    Route::get('/notes/my', [NoteController::class, 'myNotes']);

    Route::get('/notes', [NoteController::class, 'index']);
    Route::get('/notes/{id}', [NoteController::class, 'show']);
    Route::post('/notes/bulk-delete', [NoteController::class, 'bulkDestroy']);
    // CRUD Notes
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);

    Route::get('/music/search', [MusicController::class, 'search']);

    // User Profile
    Route::get('/users/current', [UserController::class, 'show']);
    Route::patch('/users/current', [UserController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Kelola User
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Nanti bisa tambah: Route::delete('/notes/{id}', ...) untuk hapus pesan kasar
});

// ROUTE KHUSUS UNTUK BYPASS CORS IMAGE;
Route::get('/image-proxy', function (Request $request) {
    $url = $request->query('url');
    if (!$url) {
        return response()->json(['error' => 'URL required'], 400);
    }

    $baseUrl = url('/storage');
    $relativePath = str_replace($baseUrl, '', $url);
    $relativePath = ltrim($relativePath, '/');

    if (Storage::disk('public')->exists($relativePath)) {
        $path = Storage::disk('public')->path($relativePath);

        // --- PERBAIKAN: Tambahkan Header Anti-Cache ---
        return response()->file($path, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }

    return response()->json(['error' => 'Image not found'], 404);
});
