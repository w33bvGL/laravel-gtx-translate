<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Tests;

use Anidzen\GoogleTranslateScraper\Facades\TextTranslator;
use Anidzen\GoogleTranslateScraper\Providers\GoogleTranslateScraperServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Orchestra\Testbench\TestCase;

class UnitTest extends TestCase
{
    protected Logger $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = new Logger('test_logger');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../storage/Logs/google-translate-scraper.log', Logger::DEBUG));
    }

    public function test_example(): void
    {
        $this->assertEquals(4, 2 + 2);
    }

    protected function getPackageProviders($app): array
    {
        return [
            GoogleTranslateScraperServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'GoogleTranslateScraper' => TextTranslator::class,
        ];
    }
}
