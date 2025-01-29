<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Traits;

trait HasValidateLanguage
{
    public function isSupportedLanguage(string $language): bool
    {
        $supportedLanguages = config('googleTranslateScraper.supported_languages');

        return in_array($language, $supportedLanguages, true);
    }
}
