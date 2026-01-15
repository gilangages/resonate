<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactSupportMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'subject' => 'required|string|max:100', // Bug Report / Saran / Lainnya
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // 2. Siapkan Data
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            // 3. Kirim Email ke Developer (Email Kamu)
            // Pastikan MAIL_FROM_ADDRESS di .env sudah diisi
            // Kita kirim ke alamat yang diset di .env sebagai admin, atau hardcode emailmu di sini sementara
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@resonate.com');

            Mail::to($adminEmail)->send(new ContactSupportMail($data));

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim! Terima kasih atas masukan Anda.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan. Silakan coba lagi nanti.',
                'debug' => $e->getMessage(), // Hapus line ini saat production
            ], 500);
        }
    }
}
