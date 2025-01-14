<?php

namespace AniMik\MalCrawler\Tests\Unit;

use AniMik\MalCrawler\Facades\AnimeCrawler;
use AniMik\MalCrawler\Tests\UnitTest;

class CrawlGenresTest extends UnitTest
{
    protected string $responseDirectory;

    protected string $genresResponseFile;

    protected string $explicitGenresResponseFile;

    protected string $genreDescriptionResponseFile;

    protected string $themesResponseFile;

    protected string $demographicsResponseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responseDirectory = __DIR__.'/../../storage/response/Anime/';
        $this->genresResponseFile = $this->responseDirectory.'genres_response.json';
        $this->explicitGenresResponseFile = $this->responseDirectory.'explicit_genres_response.json';
        $this->genreDescriptionResponseFile = $this->responseDirectory.'genres_description_response.json';
        $this->themesResponseFile = $this->responseDirectory.'themes_response.json';
        $this->demographicsResponseFile = $this->responseDirectory.'demographics_response.json';
    }

    public function test_it_crawls_genres(): void
    {
        $genresResponse = AnimeCrawler::crawlGenres();
        $genres = $this->decodeAndValidateJson($genresResponse);
        $this->saveResponseToFile($genres, $this->genresResponseFile);
        $this->assertFileExists($this->genresResponseFile);
        $this->logMessage('Genres response saved successfully.');
    }

    public function test_it_crawls_explicit_genres(): void
    {
        $explicitGenresResponse = AnimeCrawler::crawlExplicitGenres();
        $explicitGenres = $this->decodeAndValidateJson($explicitGenresResponse);
        $this->saveResponseToFile($explicitGenres, $this->explicitGenresResponseFile);
        $this->assertFileExists($this->explicitGenresResponseFile);
        $this->logMessage('Explicit genres response saved successfully.');
    }

    public function test_it_crawls_genre_description(): void
    {
        $genreDescriptionResponse = AnimeCrawler::crawlGenreDescription(1);
        $genreDescription = $this->decodeAndValidateJson($genreDescriptionResponse);
        $this->saveResponseToFile($genreDescription, $this->genreDescriptionResponseFile);
        $this->assertFileExists($this->genreDescriptionResponseFile);
        $this->logMessage('Genre description response saved successfully.');
    }

    public function test_it_crawls_themes(): void
    {
        $themesResponse = AnimeCrawler::crawlThemes();
        $themes = $this->decodeAndValidateJson($themesResponse);
        $this->saveResponseToFile($themes, $this->themesResponseFile);
        $this->assertFileExists($this->themesResponseFile);
        $this->logMessage('Themes response saved successfully.');
    }

    public function test_it_crawls_demographics(): void
    {
        $demographicsResponse = AnimeCrawler::crawlDemographics();
        $demographics = $this->decodeAndValidateJson($demographicsResponse);
        $this->saveResponseToFile($demographics, $this->demographicsResponseFile);
        $this->assertFileExists($this->demographicsResponseFile);
        $this->logMessage('Demographics response saved successfully.');
    }
}
