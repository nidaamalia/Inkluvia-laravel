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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash-exp'),
        'auto_caption' => env('GEMINI_AUTO_CAPTION', true),
        'caption_max_words' => env('GEMINI_CAPTION_MAX_WORDS', 20),
        'max_images_per_page' => env('GEMINI_MAX_IMAGES_PER_PAGE', 1),
        'min_image_area_ratio' => env('GEMINI_MIN_IMAGE_AREA_RATIO', 0.01),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', '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'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.3),
    ],

];
