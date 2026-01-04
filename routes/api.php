<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MusicController;
use App\Http\Controllers\Api\Auth\NoteController;
use App\Http\Controllers\Api\Auth\RegisterController;
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
Route::middleware('auth:sanctum')
    ->delete('/users/logout', LogoutController::class);

//protected route
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

//create note
// 🌍 PUBLIC
Route::get('/notes', [NoteController::class, 'index']);
Route::get('/notes/{id}', [NoteController::class, 'show']);

// 🔐 AUTH
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
});

//music
Route::get('/music/search', [MusicController::class, 'search']);

Route::middleware('auth:sanctum')->group(function () {
    // ... route notes yang sudah ada ...

    // Tambahkan fitur Edit Profile di sini
    Route::get('/users/current', [UserController::class, 'show']);
    Route::patch('/users/current', [UserController::class, 'update']);

    // ... route logout ...
});
