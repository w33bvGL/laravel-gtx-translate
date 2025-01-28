<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------
    | Base URL
    |----------------------------------------------------------------------
    |
    | The base URL for accessing the main resource.
    |
    */
    'base_url' => env('GOOGLE_TRANSLATE_SCRAPER_BASE_URL', 'https://translate.google.com'),

    /*
    |----------------------------------------------------------------------
    | Request Timeout
    |----------------------------------------------------------------------
    |
    | The timeout in seconds for requests made by the scraper.
    |
    */
    'timeout' => env('GOOGLE_TRANSLATE_SCRAPER_TIMEOUT', 30),

    /*
    |----------------------------------------------------------------------
    | Proxy Settings
    |----------------------------------------------------------------------
    |
    | If you're using proxies to scrape, you can set them here.
    |
    */
    'proxy' => env('GOOGLE_TRANSLATE_SCRAPER_PROXY', null),

    /*
    |----------------------------------------------------------------------
    | Supported Languages
    |----------------------------------------------------------------------
    |
    | List of supported languages by the Google Translate Scraper.
    |
    */
    'supported_languages' => [
        'Armenian' => 'hy',
        'Chinese (Traditional)' => 'zh-TW',
        'Chinese (Simplified)' => 'zh-CN',
        'Japanese' => 'ja',
        'English' => 'en',
        'Russian' => 'ru',
    ],
];
