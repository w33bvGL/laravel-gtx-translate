<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

class TextTranslator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translate';
    }

    public static function textTranslate(): JsonResponse
    {
        return app('translate')['text']->ScrapeText();
    }
}
