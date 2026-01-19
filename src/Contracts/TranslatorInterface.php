<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Contracts;

use W33bvgl\GtxTranslate\DTO\TranslationResult;
use W33bvgl\GtxTranslate\Enums\Language;

interface TranslatorInterface
{
    public function translate(string $text, string|Language $target, string|Language $source = 'auto'): TranslationResult;
}
