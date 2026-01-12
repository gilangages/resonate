<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * 1. Ambil daftar notifikasi user (Optimized for Dropdown)
     * GET /api/notifications
     */
    public function index(Request $request): JsonResponse
    {
        // OPTIMISASI: Gunakan take(10) -> get() alih-alih paginate()
        // Ini menghindari query "count(*)" yang berat jika data ribuan.
        // Dropdown hanya butuh intip 10 data terbaru.
        $notifications = $request->user()
            ->notifications() // Mengambil dari tabel 'notifications' via model User
            ->latest()
            ->take(10)
            ->get();

        // Kita bungkus dalam 'data' agar struktur JSON tetap sama
        // dengan format pagination sebelumnya (sehingga frontend tidak error baca .data)
        return response()->json([
            'data' => $notifications,
        ]);
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

    public function markAllRead(Request $request)
    {
        // Cara Laravel yang sangat efisien (1 query update langsung ke semua yang unread)
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->json(['message' => 'Semua notifikasi telah ditandai sudah dibaca.']);
    }

    public function getAll(Request $request): JsonResponse
    {
        // Gunakan paginate() agar bisa ada halaman 1, 2, 3, dst.
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15); // Tampilkan 15 per halaman

        return response()->json($notifications);
    }
}
