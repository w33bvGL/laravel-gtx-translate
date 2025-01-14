<?php

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\AnimeCrawler;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlStudiosTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $studiosResponseFile;

    protected string $studiosInformationResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory = __DIR__.'/../../storage/response/Anime/';
        $this->studiosResponseFile = $this->responseDirectory.'studios_response.json';
        $this->studiosInformationResponseFile = $this->responseDirectory.'studios_information_response.json';
    }

    public function test_it_crawls_studios(): void
    {
        $studiosResponse = AnimeCrawler::crawlStudios();
        $studios = $this->decodeAndValidateJson($studiosResponse);
        $this->saveResponseToFile($studios, $this->studiosResponseFile);
        $this->assertFileExists($this->studiosResponseFile);
        $this->logMessage('Studios response saved successfully.');
    }

    public function test_it_crawls_studio_information(): void
    {
        $studiosInformationResponse = AnimeCrawler::crawlStudioInformation(2);
        $studiosInformation = $this->decodeAndValidateJson($studiosInformationResponse);
        $this->saveResponseToFile($studiosInformation, $this->studiosInformationResponseFile);
        $this->assertFileExists($this->studiosInformationResponseFile);
        $this->logMessage('Studio Information response saved successfully.');
    }
}
