<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Tests\Unit;

use Anidzen\GoogleTranslateScraper\Facades\TextTranslator;
use Anidzen\GoogleTranslateScraper\Tests\UnitTest;

class TextTranslationTest extends UnitTest
{
    public function test_it_text_translate(): void
    {
        $this->logger->info('Calling the text translation service...');

        $result = TextTranslator::translate('ru', 'hy', 'привет');

        $this->logger->info('Translation result: '.$result);

        $this->assertNotEmpty($result, 'Translation result should not be empty');
    }
}
