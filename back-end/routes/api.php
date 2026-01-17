<?php

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AudioStreamController;
use App\Http\Controllers\Api\Auth\AppealController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ImageProxyController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\NoteReplyController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/debug-config', function () {
    return [
        '1_env_cloudinary_url' => env('CLOUDINARY_URL'), // Kalau ini muncul isinya, berarti BELUM KEHAPUS di Render
        '2_config_cloud_url' => config('cloudinary.cloud_url'), // Harusnya null
        '3_cloud_name' => config('cloudinary.client.cloud_name'), // Harusnya nama cloud kamu (resonate-app?)
        '4_api_key_check' => empty(config('cloudinary.client.api_key')) ? 'KOSONG' : 'ADA',
        '5_cloudinary_disk' => config('filesystems.disks.cloudinary') ? 'ADA' : 'TIDAK ADA',
    ];
});

// === ROUTE KHUSUS PING / HEALTH CHECK ===
// Diakses oleh UptimeRobot / Cron-job agar server tidak tidur
Route::get('/health', function () {
    try {
        // Coba koneksi ke database (ringan)
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'ok',
            'server' => 'alive',
            'database' => 'connected',
            'timestamp' => now(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/test', function () {
    return response()->json(['status' => 'API works!']);
});

Route::get('/test-sanctum', function () {
    return 'Sanctum OK';
});

// Route Public (Register/Login)
Route::post('/users', RegisterController::class);
Route::post('/users/login', LoginController::class);
Route::post('/users/appeal', [AppealController::class, 'sendAppeal']);
// Contact / Report Bug
Route::post('/contact', [ContactController::class, 'sendMessage']);

// get Global Notes (Bisa diakses siapa saja)
Route::get('/notes/global', [NoteController::class, 'globalIndex']);
Route::get('/stream/{trackId}', [AudioStreamController::class, 'stream']);

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// 🔐 AUTH ROUTES (Prioritas Tinggi)
// ⚠️ PERUBAHAN DI SINI: Tambahkan 'check.banned' setelah 'auth:sanctum'
Route::middleware(['auth:sanctum', 'check.banned'])->group(function () {
    Route::delete('/users/logout', LogoutController::class);

    Route::get('/notes/my', [NoteController::class, 'myNotes']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/all', [NotificationController::class, 'getAll']);

    // ✅ POSISI 'my' AMAN DI SINI

    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes/bulk-delete', [NoteController::class, 'bulkDestroy']);

    // ROUTE BARU UNTUK REPLY TERPISAH
    Route::post('/notes/{id}/reply', [NoteReplyController::class, 'store']);
    Route::delete('/replies/{id}', [NoteReplyController::class, 'destroy']);

    // CRUD Notes
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);

    Route::get('/music/search', [MusicController::class, 'search']);

    // User Profile
    Route::get('/users/current', [UserController::class, 'show']);
    Route::patch('/users/current', [UserController::class, 'update']);

    //delete
    Route::delete('/users/current', [UserController::class, 'destroy']);
});

// ⚠️ PERUBAHAN DI SINI: Tambahkan 'check.banned' juga untuk keamanan ganda
Route::middleware(['auth:sanctum', 'check.banned', 'admin'])->prefix('admin')->group(function () {
    // Kelola User
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    // Moderasi Notes
    Route::get('/notes', [AdminUserController::class, 'indexNotes']);
    Route::delete('/notes/{id}', [AdminUserController::class, 'destroyNote']);
    Route::patch('/users/{id}/restore', [AdminUserController::class, 'restore']);
    Route::post('/ban-by-note/{noteId}', [AdminUserController::class, 'banUserByNoteId']);
});

// ROUTE KHUSUS UNTUK BYPASS CORS IMAGE;
Route::get('/image-proxy', [ImageProxyController::class, 'proxy']);
Route::get('/notes/{id}', [NoteController::class, 'show']);
