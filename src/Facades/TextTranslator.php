<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * The facade methods are intended for both general use cases (e.g., retrieving a list of genres)
 * and more specific cases, such as fetching detailed anime data by its MAL ID. It also supports filtering
 * data based on various criteria, such as anime status, age ratings, and more.
 *
 * This facade works by providing a simplified interface to the underlying anime crawling logic,
 * ensuring that the implementation details are abstracted away from the rest of the application.
 * All data crawled via this facade is returned as JSON responses, making it easy to integrate into
 * any application that needs to process or display anime-related information.
 *
 * Example usage:
 * - `TextTranslator::crawlGenres()`
 * - `TextTranslator::crawlAnime(12345)`
 * - `TextTranslator::crawlValidAnimeIds()`
 */
class TextTranslator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translate';
    }

    /**
     * Crawls anime genres from MyAnimeList.
     * This method retrieves all anime genres from MyAnimeList.
     * The result is returned as a JSON response.
     *
     *  Example usage:
     *  - `TextTranslator::crawlGenres()`
     */
    public static function textTranslate(): JsonResponse
    {
        return app('translate')['text']->crawlGenres();
    }
}
