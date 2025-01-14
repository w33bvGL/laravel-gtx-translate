<?php

namespace AniMik\MalCrawler\Services\Genre;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnimeGenres extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Извлекает и возвращает данные о жанрах аниме.
     *
     * Extracts and returns the genre data for anime.
     *
     * Этот метод извлекает все жанры аниме и возвращает их в формате JSON.
     * This method extracts all anime genres and returns them in JSON format.
     */
    public function crawlGenres(): JsonResponse
    {
        return $this->crawlGenreData('.anime-manga-search .genre-link', 0);
    }

    /**
     * Извлекает и возвращает данные о явных жанрах аниме.
     *
     * Extracts and returns the explicit genre data for anime.
     *
     * Этот метод извлекает явные жанры аниме и возвращает их в формате JSON.
     * This method extracts explicit anime genres and returns them in JSON format.
     */
    public function crawlExplicitGenres(): JsonResponse
    {
        return $this->crawlGenreData('.anime-manga-search .genre-link', 1);
    }

    /**
     * Извлекает и возвращает данные о тематике аниме.
     *
     * Extracts and returns the theme data for anime.
     *
     * Этот метод извлекает все тематические жанры аниме и возвращает их в формате JSON.
     * This method extracts all theme genres for anime and returns them in JSON format.
     */
    public function crawlThemes(): JsonResponse
    {
        return $this->crawlGenreData('.anime-manga-search .genre-link', 2);
    }

    /**
     * Извлекает и возвращает данные о демографической целевой аудитории аниме.
     *
     * Extracts and returns the demographic data for anime.
     *
     * Этот метод извлекает демографические жанры аниме, такие как "Сёдзе", "Сёнэн" и т.д., и возвращает их в формате JSON.
     * This method extracts demographic genres for anime, such as "Shoujo", "Shounen", etc., and returns them in JSON format.
     */
    public function crawlDemographics(): JsonResponse
    {
        return $this->crawlGenreData('.anime-manga-search .genre-link', 3);
    }

    /**
     * Извлекает и возвращает описание жанра по его ID.
     *
     * Extracts and returns the genre description by its MAL ID.
     *
     * Этот метод извлекает описание жанра аниме по его уникальному идентификатору на сайте MyAnimeList.
     * This method extracts the genre description for anime by its unique MAL ID.
     */
    public function crawlGenreDescription(int $malId): JsonResponse
    {
        $baseUrl = config('malCrawler.base_url');
        $genresUrl = config('malCrawler.genre');
        $url = $baseUrl.$genresUrl.'/'.$malId;

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([
                'malId' => $malId,
                'description' => config('malCrawler.not_found'),
            ]);
        }

        $crawler = new Crawler($content);
        $description = $crawler->filter('#content .genre-description')->count() > 0
          ? $crawler->filter('#content .genre-description')->text()
          : null;

        return response()->json([
            'malId' => $malId,
            'description' => $description ? trim($description) : config('malCrawler.not_found'),
        ]);
    }

    /**
     * Общий метод для извлечения данных жанров по указанному селектору.
     *
     * A general method to extract genre data by the provided selector.
     *
     * Этот метод делает HTTP запрос для получения HTML-контента страницы,
     * затем извлекает данные жанров с помощью библиотеки DomCrawler.
     * This method makes an HTTP request to fetch the HTML content of the page,
     * then extracts genre data using the DomCrawler library.
     */
    protected function crawlGenreData(string $genreSelector, ?int $index = null): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.genres_url');

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);
        $genreItems = $crawler->filter($genreSelector);

        if ($index !== null) {
            $genreItems = $genreItems->eq($index);
        }

        $idCounter = 1;
        $genres = $genreItems->filter('.genre-name-link')->each(function ($node) use (&$idCounter) {
            preg_match('/\/(\d+)\/(.+)/', $node->attr('href'), $matches);

            if (empty($matches[1])) {
                return null;
            }

            $name = $node->text();
            preg_match('/(.*) \((\d+[,0-9]*)\)/', $name, $nameMatches);

            $slug = isset($matches[2]) ? $this->generateSlug($matches[2]) : '';

            return [
                'id' => $idCounter++,
                'malId' => (int) $matches[1],
                'slug' => $slug,
                'name' => $nameMatches[1] ?? $name,
                'titlesCount' => isset($nameMatches[2]) ? (int) str_replace(',', '', $nameMatches[2]) : 0,
                'link' => $node->attr('href'),
            ];
        });

        return response()->json(array_values(array_filter($genres)));
    }
}
