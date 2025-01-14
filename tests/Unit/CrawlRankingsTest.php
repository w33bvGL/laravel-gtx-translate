<?php

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\AnimeCrawler;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlRankingsTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $rankingsResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory = __DIR__.'/../../storage/response/Anime/';
        $this->rankingsResponseFile = $this->responseDirectory.'rankings_response.json';
    }

    public function test_it_crawls_rankings(): void
    {
        $rankingsResponse = AnimeCrawler::crawlRankings();
        $rankings = $this->decodeAndValidateJson($rankingsResponse);
        $this->saveResponseToFile($rankings, $this->rankingsResponseFile);
        $this->assertFileExists($this->rankingsResponseFile);
        $this->logMessage('Rankings response saved successfully.');
    }
}
