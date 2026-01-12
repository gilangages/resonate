<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * 1. Ambil daftar notifikasi user
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        // Mengambil semua notifikasi milik user yang sedang login
        // Diurutkan dari yang terbaru, dan dipagination (10 per halaman)
        $notifications = $request->user()
            ->notifications() // Mengambil dari tabel 'notifications' via model User
            ->latest()
            ->paginate(10);

        return response()->json($notifications);
    }

    /**
     * 2. Hitung notifikasi yang belum dibaca (Untuk Badge Lonceng)
     * GET /api/notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * 3. Tandai notifikasi sebagai sudah dibaca
     * POST /api/notifications/mark-read
     * Body: { "id": "uuid-notifikasi" } atau kosong untuk "Mark All"
     */
    public function markAsRead(Request $request): JsonResponse
    {
        // Jika Frontend mengirim ID spesifik, tandai satu saja
        if ($request->has('id')) {
            $notification = $request->user()
                ->notifications()
                ->where('id', $request->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }

            return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
        }

        // Jika tidak ada ID, tandai SEMUA sebagai sudah dibaca (Mark All as Read)
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
