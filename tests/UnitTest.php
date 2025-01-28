<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Tests;

use Anidzen\GoogleTranslateScraper\Facades\TextTranslator;
use Anidzen\GoogleTranslateScraper\Providers\GoogleTranslateScraperServiceProvider;
use Orchestra\Testbench\TestCase;

class UnitTest extends TestCase
{
    protected string $mainLogFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mainLogFile = $this->generateLogFilePath();

        $directory = dirname($this->mainLogFile);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($this->mainLogFile, 'Log file created at: '.date('Y-m-d H:i:s').PHP_EOL);
    }

    protected function generateLogFilePath(): string
    {
        $timestamp = date('Y-m-d_H');

        return __DIR__."/../storage/Logs/malCrawler_{$timestamp}.log";
    }

    protected function logMessage(string $message): void
    {
        $logMessage = date('Y-m-d H:i:s').' - '.$message.PHP_EOL;
        file_put_contents($this->mainLogFile, $logMessage, FILE_APPEND);
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
        if (! file_exists($filePath)) {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents($filePath, $json);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($filePath, $json);
    }
}
