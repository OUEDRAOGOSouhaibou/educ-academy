<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'webpush' => [
        'vapid' => [
            'subject' => env('VAPID_SUBJECT', 'mailto:admin@educ-academy.alwaysdata.net'),
            'public_key' => env('VAPID_PUBLIC_KEY', 'BG-qiUaedG7YBKn3npY3WAlkcEYvhjug9uOvWK_p3HzaooAdw67mMlg8MXCj6b8PE8FcSf91Xqjj9iz8XRkMhR4'),
            'private_key' => env('VAPID_PRIVATE_KEY', 'Uw_iZEctq8zBzz49uSROCUf7U3ZGKMWln_FRhkvnY8c'),
        ],
    ],

];
