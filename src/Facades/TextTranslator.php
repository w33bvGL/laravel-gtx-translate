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

    public static function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        return app('translate')['text']->translate($sourceLanguage, $targetLanguage, $text);
    }
}
