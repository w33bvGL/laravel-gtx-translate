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

        // Создание экземпляра Monolog
        $this->logger = new Logger('test_logger');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../storage/Logs/test.log', Logger::DEBUG));

        // Логируем сообщение
        $this->logger->info('Test setup initialized at: '.now());
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

    protected function decodeAndValidateJson($response)
    {
        $decodedJson = json_decode($response->getContent(), true);
        $this->assertJson($response->getContent());
        $this->assertNotEmpty($decodedJson);

        return $decodedJson;
    }

    protected function saveResponseToFile($data, $filePath): void
    {
        $this->logger->info('Saving response data to: '.$filePath);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($filePath, $json);
    }
}
