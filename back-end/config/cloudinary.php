<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud
    | hosted media management service for all your file uploads, storage,
    | delivery and transformation needs.
    |
     */

    'cloud_url' => null,

    /**
     * Upload Preset Name
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /**
     * Notification URL
     */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /**
     * Cloudinary URL for the secure distribution of images.
     * It is used to deliver the images from a custom domain/subdomain.
     */
    'secure_url' => env('CLOUDINARY_SECURE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration (Advanced)
    |--------------------------------------------------------------------------
    |
    | Here you can configure the Cloudinary client implementation.
    |
     */
    'client' => [
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    ],

    'admin' => [
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    ],
];
