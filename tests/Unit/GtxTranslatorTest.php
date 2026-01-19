<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use W33bvgl\GtxTranslate\Contracts\TranslatorInterface;
use W33bvgl\GtxTranslate\Exceptions\TranslationRequestException;
use W33bvgl\GtxTranslate\Providers\GtxTranslateServiceProvider;

class GtxTranslatorTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [GtxTranslateServiceProvider::class];
    }

    public function test_it_translates_text_correctly()
    {
        $fakeResponse = [[['Hello World', 'Привет мир']]];

        Http::fake([
            'translate.googleapis.com/*' => Http::response($fakeResponse, 200),
        ]);

        /** @var TranslatorInterface $translator */
        $translator = app(TranslatorInterface::class);

        $result = $translator->translate('Привет мир', 'en');

        $this->assertEquals('Hello World', $result->translatedText);
        $this->assertEquals('en', $result->targetLanguage);
    }

    public function test_it_throws_exception_on_empty_text()
    {
        $this->expectException(TranslationRequestException::class);

        $translator = app(TranslatorInterface::class);
        $translator->translate('', 'en');
    }

    public function test_it_handles_google_errors()
    {
        Http::fake([
            'translate.googleapis.com/*' => Http::response('Too Many Requests', 429),
        ]);

        $this->expectException(TranslationRequestException::class);
        $this->expectExceptionMessage('429');

        /** @var TranslatorInterface $translator */
        $translator = app(TranslatorInterface::class);
        $translator->translate('test', 'en');
    }

    public function test_it_asks_for_details_if_arguments_are_missing(): void
    {
        Http::fake([
            '*' => Http::response([[['Hello', 'Привет']]]),
        ]);

        $this->artisan('gtx:translate')
            ->expectsQuestion('What text would you like to translate?', 'Привет')
            ->expectsChoice('Select source language', 'auto', ['auto', 'en', 'ru', 'hy', 'ja', 'zh-CN', 'zh-TW', 'fr', 'de', 'es'])
            ->expectsChoice('Select target language', 'en', ['en', 'ru', 'hy', 'ja', 'zh-CN', 'zh-TW', 'fr', 'de', 'es'])
            ->assertExitCode(0);
    }
}
