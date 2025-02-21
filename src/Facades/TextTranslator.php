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

    /**
     * Translates text from one language to another.
     *
     * This method performs the translation process, converting the given text from the source
     * language to the target language using the translation service. It handles all necessary steps,
     * including data validation and interaction with the translation API.
     *
     * @param string $sourceLanguage The source language.
     * @param string $targetLanguage The target language.
     * @param string $text The text to be translated.
     * @return JsonResponse The translation result in ?array format.
     */
    public static function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        return app('translate')['text']->translate($sourceLanguage, $targetLanguage, $text);
    }
}
