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

        $result = TextTranslator::translate('ru', 'ww', 'привет как дела?');

        $data  = json_decode($result->getContent(), true, 512, JSON_UNESCAPED_UNICODE);

        $this->logger->info(json_encode($data, JSON_UNESCAPED_UNICODE));

        $this->assertNotEmpty($result->getContent(), 'Translation result should not be empty');

    }
}
