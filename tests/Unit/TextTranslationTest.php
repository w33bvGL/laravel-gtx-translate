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

        $result = TextTranslator::translate('ru', 'hy', 'привет как дела?');

        $this->logger->info(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $data = $result->getData(true);
        $this->assertNotEmpty($data, 'Translation result should not be empty');
    }
}
