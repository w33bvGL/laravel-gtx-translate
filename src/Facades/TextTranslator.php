<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Facades;

use Anidzen\GoogleTranslateScraper\Exceptions\TextTranslationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use Exception;

class TextTranslator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translate';
    }

    /**
     * Переводит текст с одного языка на другой.
     *
     * Этот метод вызывает соответствующий метод перевода на сервисе, обеспечивая пользователю
     * доступ к функционалу перевода текста.
     *
     * @param string $sourceLanguage Исходный язык.
     * @param string $targetLanguage Целевой язык.
     * @param string $text Текст, который нужно перевести.
     *
     * @return JsonResponse Ответ с результатами перевода в формате JSON.
     *
     * @throws TextTranslationException При ошибках в процессе перевода.
     */
    public static function translate(string $sourceLanguage, string $targetLanguage, string $text): JsonResponse
    {
        return app('translate')['text']->translate($sourceLanguage, $targetLanguage, $text);
    }
}
