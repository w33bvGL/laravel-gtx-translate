<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for accessing the main resource. By default, this points to
    | MyAnimeList. You can override this value in the .env file by setting the
    | MAL_CRAWLER_BASE_URL parameter.
    |
    */
    'base_url' => env('MAL_CRAWLER_BASE_URL', 'https://myanimelist.net'),
];
