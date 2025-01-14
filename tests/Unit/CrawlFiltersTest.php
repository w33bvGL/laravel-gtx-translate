<?php

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\AnimeCrawler;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlFiltersTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $typesResponseFile;

    protected string $statusResponseFile;

    protected string $ratedResponseFile;

    protected string $columnsResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory = __DIR__.'/../../storage/response/Anime/';
        $this->typesResponseFile = $this->responseDirectory.'anime_types_response.json';
        $this->statusResponseFile = $this->responseDirectory.'anime_status_response.json';
        $this->ratedResponseFile = $this->responseDirectory.'anime_rated_response.json';
        $this->columnsResponseFile = $this->responseDirectory.'anime_columns_response.json';
    }

    public function test_it_crawls_types()
    {
        $typesResponse = AnimeCrawler::crawlTypes();
        $types = $this->decodeAndValidateJson($typesResponse);
        $this->saveResponseToFile($types, $this->typesResponseFile);
        $this->assertFileExists($this->typesResponseFile);
        $this->logMessage('MalCrawler types response saved successfully.');
    }

    public function test_it_crawls_status()
    {
        $statusResponse = AnimeCrawler::crawlStatus();
        $status = $this->decodeAndValidateJson($statusResponse);
        $this->saveResponseToFile($status, $this->statusResponseFile);
        $this->assertFileExists($this->statusResponseFile);
        $this->logMessage('MalCrawler status response saved successfully.');
    }

    public function test_it_crawls_rated()
    {
        $ratedResponse = AnimeCrawler::crawlRated();
        $rated = $this->decodeAndValidateJson($ratedResponse);
        $this->saveResponseToFile($rated, $this->ratedResponseFile);
        $this->assertFileExists($this->ratedResponseFile);
        $this->logMessage('MalCrawler rated response saved successfully.');
    }

    public function test_it_crawls_columns()
    {
        $columnsResponse = AnimeCrawler::crawlColumns();
        $columns = $this->decodeAndValidateJson($columnsResponse);
        $this->saveResponseToFile($columns, $this->columnsResponseFile);
        $this->assertFileExists($this->columnsResponseFile);
        $this->logMessage('MalCrawler columns response saved successfully.');
    }
}
