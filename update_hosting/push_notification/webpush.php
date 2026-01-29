<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys
    |--------------------------------------------------------------------------
    |
    | VAPID (Voluntary Application Server Identification) keys are used to
    | identify the server sending push notifications. Generate new keys using:
    | php artisan webpush:vapid
    |
    | Or generate online at: https://web-push-codelab.glitch.me/
    |
    */
    
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', env('APP_URL', 'https://koperasi.example.com')),
        'public_key' => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | GCM/FCM Settings (Optional - for older Android)
    |--------------------------------------------------------------------------
    */
    
    'gcm' => [
        'key' => env('GCM_KEY', ''),
        'sender_id' => env('GCM_SENDER_ID', ''),
    ],
];
