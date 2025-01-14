<?php

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\AnimeCrawler;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlSeasonsTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $seasonsResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory = __DIR__.'/../../storage/response/Anime/';
        $this->seasonsResponseFile = $this->responseDirectory.'seasons_response.json';
    }

    public function test_it_crawls_seasons(): void
    {
        $seasonsResponse = AnimeCrawler::crawlSeasons();
        $seasons = $this->decodeAndValidateJson($seasonsResponse);
        $this->saveResponseToFile($seasons, $this->seasonsResponseFile);
        $this->assertFileExists($this->seasonsResponseFile);
        $this->logMessage('Season response saved successfully.');
    }
}
