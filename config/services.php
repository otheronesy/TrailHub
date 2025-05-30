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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'api_base_url' => 'https://slack.com/api/',
        'user_token' => env('NIKA_TALK_USER_TOKEN'),
        'default_channel' => env('NIKA_TALK_CHANNEL_ID'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'calendar_base_url' => 'https://www.googleapis.com/calendar/v3',
        'drive_base_url' => 'https://www.googleapis.com/drive/v3',
    ],

    'mylebook' => [
        'base_url' => env('MYLE_BOOK_API_BASE'),
        'api_key' => env('MYLE_BOOK_API_KEY'),
    ],

    'openai' => [
        'api_base_url' => 'https://api.openai.com/v1/',
        'api_key' => env('JATRAIL_API_KEY'),
    ],

];
