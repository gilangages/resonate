<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

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

// 🔐 AUTH ROUTES (Prioritas Tinggi)
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/users/logout', LogoutController::class);

    // ✅ POSISI 'my' AMAN DI SINI
    // Karena route {id} belum didefinisikan di atasnya
    Route::get('/notes/my', [NoteController::class, 'myNotes']);

    Route::get('/notes', [NoteController::class, 'index']);
    Route::get('/notes/{id}', [NoteController::class, 'show']);
    // CRUD Notes
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);

    Route::get('/music/search', [MusicController::class, 'search']);

    // User Profile
    Route::get('/users/current', [UserController::class, 'show']);
    Route::patch('/users/current', [UserController::class, 'update']);
});

// 🌍 PUBLIC ROUTES (List & Detail)
// ⚠️ WAJIB TARUH DI PALING BAWAH
// Agar '/notes/my' tidak tertangkap oleh '/notes/{id}'
