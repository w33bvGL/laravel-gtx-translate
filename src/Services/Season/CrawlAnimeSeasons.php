<?php

namespace AniMik\MalCrawler\Services\Season;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnimeSeasons extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Получить сезоны с указанного URL
     *
     * Get seasons from the specified URL.
     *
     * Этот метод используется для получения сезонов с указанного URL.
     * Он вызывает `crawlSeasonData` с заранее определённым селектором.
     *
     * This method is used to get seasons from the specified URL.
     * It calls `crawlSeasonData` with a predefined selector.
     */
    public function crawlSeasons(): JsonResponse
    {
        return $this->crawlSeasonData('.js-categories-seasonal .anime-seasonal-byseason');
    }

    /**
     * Общий метод для извлечения сезонов
     *
     * A General Method for Season Extraction.
     *
     * Этот метод используется для извлечения данных сезонов с помощью указанного CSS селектора.
     * Метод отправляет HTTP запрос по указанному URL, извлекает данные с помощью DOM парсера
     * и формирует итоговый список сезонов с их id, slug, названием и URL.
     *
     * This method is used to extract season data using the specified CSS selector.
     * It sends an HTTP request to the provided URL, extracts the data using the DOM parser,
     * and forms a final list of seasons with their id, slug, season name, and URL.
     */
    protected function crawlSeasonData(string $selector): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.season_url');

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $seasonItems = $crawler->filter($selector.' a');

        $idCounter = 1;

        $seasons = $seasonItems->each(function ($node) use (&$idCounter) {
            $slug = $this->generateSlug(trim($node->text()));

            return [
                'id' => $idCounter++,
                'slug' => $slug,
                'season' => trim($node->text()),
                'url' => $node->attr('href'),
            ];
        });

        $seasons = array_filter($seasons);

        return response()->json(array_values($seasons));
    }
}
