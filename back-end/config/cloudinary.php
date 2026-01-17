<?php

/*
|--------------------------------------------------------------------------
| Cloudinary Configuration
|--------------------------------------------------------------------------
|
| Config ini memaksa Laravel untuk membaca CLOUDINARY_URL dari environment
| variable dengan benar, tanpa menebak-nebak.
|
 */

return [
    'cloud_url' => env('CLOUDINARY_URL'),
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),
];
