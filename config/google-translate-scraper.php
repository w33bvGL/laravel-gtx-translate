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
    | Hidden Api Base URL
    |----------------------------------------------------------------------
    |
    | The google translate api v1 hidden api base URL.
    |
    */
    'hidden_api_base_url' => env('GOOGLE_TRANSLATE_SCRAPER_HIDDEN_API_BASE_URL', 'https://translate.googleapis.com'),

    /*
    |----------------------------------------------------------------------
    | Request Timeout (Min and Max)
    |----------------------------------------------------------------------
    |
    | Минимальное и максимальное время ожиданидля запросов.
    | Это будет случайное значение в указанном диапазоне.
    |
    */
    'timeout_min' => env('GOOGLE_TRANSLATE_SCRAPER_TIMEOUT_MIN', 1000000),
    'timeout_max' => env('GOOGLE_TRANSLATE_SCRAPER_TIMEOUT_MAX', 5000000),

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
    | Proxy Settings
    |----------------------------------------------------------------------
    |
    | If you're using proxies to scrape, you can set them here.
    |
    */
    'text_max_length' => env('GOOGLE_TRANSLATE_SCRAPER_TEXT_MAX_LENGTH', 100),

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

    /*
   |----------------------------------------------------------------------
   | User Agents
   |----------------------------------------------------------------------
   |
   | Список User-Agent'ов для имитации различных браузеров.
   |
   */
    'user_agents' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    ],
];
