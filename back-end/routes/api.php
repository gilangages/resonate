<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\NoteController; // ✅ Benar, folder Api langsung
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'status' => 'API works!',
    ]);
});

Route::get('/test-sanctum', function () {
    return 'Sanctum OK';
});

Route::post('/users', RegisterController::class);
Route::post('/users/login', LoginController::class);

// 🌍 PUBLIC: Get Notes List & Detail
Route::get('/notes', [NoteController::class, 'index']);
Route::get('/notes/{id}', [NoteController::class, 'show']);

// 🔐 AUTH (Harus Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/users/logout', LogoutController::class);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // CRUD Notes
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);

    // 👇 PINDAHKAN KE SINI (Sekarang butuh token)
    Route::get('/music/search', [MusicController::class, 'search']);

    // User Profile
    Route::get('/users/current', [UserController::class, 'show']);
    Route::patch('/users/current', [UserController::class, 'update']);
});
