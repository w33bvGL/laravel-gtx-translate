<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------
    | Base URL
    |----------------------------------------------------------------------
    |
    | The base URL for accessing the main resource (Google Translate).
    |
    */
    'base_url' => env('GOOGLE_TRANSLATE_SCRAPER_BASE_URL', 'https://translate.google.com'),

    /*
    |----------------------------------------------------------------------
    | Hidden API Base URL
    |----------------------------------------------------------------------
    |
    | The base URL for the hidden Google Translate API v1 endpoint.
    |
    */
    'hidden_api_base_url' => env('GOOGLE_TRANSLATE_SCRAPER_HIDDEN_API_BASE_URL', 'https://translate.googleapis.com'),

    /*
    |----------------------------------------------------------------------
    | Request Timeout (Min and Max)
    |----------------------------------------------------------------------
    |
    | The minimum and maximum timeout values for requests. A random value
    | will be selected within the specified range.
    |
    */
    'timeout_min' => env('GOOGLE_TRANSLATE_SCRAPER_TIMEOUT_MIN', 1000000),
    'timeout_max' => env('GOOGLE_TRANSLATE_SCRAPER_TIMEOUT_MAX', 5000000),

    /*
    |----------------------------------------------------------------------
    | Proxy Settings
    |----------------------------------------------------------------------
    |
    | If you're using proxies for scraping, you can set them here.
    |
    */
    'proxy' => env('GOOGLE_TRANSLATE_SCRAPER_PROXY', null),

    /*
    |----------------------------------------------------------------------
    | Max Text Length
    |----------------------------------------------------------------------
    |
    | The maximum length of the text to be translated. This setting limits
    | the size of text that can be processed by the translation API.
    |
    */
    'text_max_length' => env('GOOGLE_TRANSLATE_SCRAPER_TEXT_MAX_LENGTH', 20000),

    /*
    |----------------------------------------------------------------------
    | Supported Languages
    |----------------------------------------------------------------------
    |
    | A list of supported languages by the Google Translate Scraper,
    | where the key is the language name and the value is the language code.
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
    | This helps to avoid detection and blocking when scraping.
    |
    */
    'user_agents' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    ],
];
