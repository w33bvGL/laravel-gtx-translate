<?php

declare(strict_types=1);

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\TextTranslator;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlAnimeTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $animeRangeResponseFile;

    protected string $animeResponseFile;

    protected string $animeCharactersAndStaffResponseFile;

    protected string $animeEpisodesListResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory                   = __DIR__.'/../../storage/response/Anime/';
        $this->animeRangeResponseFile              = $this->responseDirectory.'anime_range_response.json';
        $this->animeResponseFile                   = $this->responseDirectory.'anime_response.json';
        $this->animeCharactersAndStaffResponseFile = $this->responseDirectory.'anime_characters_and_staff_response.json';
        $this->animeEpisodesListResponseFile       = $this->responseDirectory.'anime_episodes_list_response.json';
    }

    public function test_it_crawls_anime_max_range(): void
    {
        $animeRangeResponse = TextTranslator::crawlValidAnimeIds();
        $animeRange         = $this->decodeAndValidateJson($animeRangeResponse);
        $this->saveResponseToFile($animeRange, $this->animeRangeResponseFile);
        $this->assertFileExists($this->animeRangeResponseFile);
        $this->logMessage('testItCrawlsAnimeMaxRange response saved successfully.');
    }

    public function test_it_crawl_anime(): void
    {
        $animeResponse = TextTranslator::crawlAnime(53126);
        $anime         = $this->decodeAndValidateJson($animeResponse);
        $this->saveResponseToFile($anime, $this->animeResponseFile);
        $this->assertFileExists($this->animeResponseFile);
        $this->logMessage('testItCrawlAnime response saved successfully.');
    }

    public function test_it_crawl_anime_characters_and_staff(): void
    {
        $animeCharactersAndStaffResponse = TextTranslator::crawlAnimeCharactersAndStaff(48736);
        $animeCharactersAndStaff         = $this->decodeAndValidateJson($animeCharactersAndStaffResponse);
        $this->saveResponseToFile($animeCharactersAndStaff, $this->animeCharactersAndStaffResponseFile);
        $this->assertFileExists($this->animeCharactersAndStaffResponseFile);
        $this->logMessage('testItCrawlAnimeCharactersAndStaff response saved successfully.');
    }

    public function test_it_crawl_anime_episodes_list(): void
    {
        $animeAnimeEpisodesListResponse = TextTranslator::crawlAnimeEpisodesList(48736);
        $animeEpisodesList              = $this->decodeAndValidateJson($animeAnimeEpisodesListResponse);
        $this->saveResponseToFile($animeEpisodesList, $this->animeEpisodesListResponseFile);
        $this->assertFileExists($this->animeEpisodesListResponseFile);
        $this->logMessage('testItCrawlAnimeEpisodesList response saved successfully.');
    }
}
