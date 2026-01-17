<?php

/*
|--------------------------------------------------------------------------
| Cloudinary Configuration (Auto-Build)
|--------------------------------------------------------------------------
|
| Daripada pusing parsing error, kita rakit URL-nya secara manual
| menggunakan variabel environment yang sudah dipastikan ADA di Render.
|
 */

// Ambil bahan-bahan dari Render
$apiKey = env('CLOUDINARY_API_KEY');
$apiSecret = env('CLOUDINARY_API_SECRET');
$cloudName = env('CLOUDINARY_CLOUD_NAME');

// Rakit URL-nya!
// Format: cloudinary://KEY:SECRET@CLOUD_NAME
$generatedUrl = null;
if ($apiKey && $apiSecret && $cloudName) {
    $generatedUrl = "cloudinary://{$apiKey}:{$apiSecret}@{$cloudName}";
}

return [

    // Masukkan URL rakitan kita ke sini
    'cloud_url' => $generatedUrl,

    // Sisanya standar (biarkan saja)
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'secure_url' => env('CLOUDINARY_SECURE_URL'),

    'client' => [
        'api_key' => $apiKey,
        'api_secret' => $apiSecret,
        'cloud_name' => $cloudName,
    ],

    'admin' => [
        'api_key' => $apiKey,
        'api_secret' => $apiSecret,
        'cloud_name' => $cloudName,
    ],
];
