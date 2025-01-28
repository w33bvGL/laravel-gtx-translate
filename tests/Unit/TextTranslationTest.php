<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Tests\Unit;

use Anidzen\GoogleTranslateScraper\Facades\TextTranslator;
use Anidzen\GoogleTranslateScraper\Tests\UnitTest;

class TextTranslationTest extends UnitTest
{
    public function text_it_text_translate(): void
    {
        TextTranslator::textTranslate();
    }
}
