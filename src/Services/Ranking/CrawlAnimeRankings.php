<?php

namespace AniMik\MalCrawler\Services\Ranking;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnimeRankings extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Получить рейтинги с указанного URL
     *
     * Get rankings from the specified URL.
     *
     * Этот метод используется для получения рейтингов с указанного URL,
     * который определяется в конфигурации проекта.
     *
     * This method is used to get rankings from the specified URL,
     * which is defined in the project configuration.
     */
    public function crawlRankings(): JsonResponse
    {
        return $this->crawlRankingData('.anime-manga-search .genre-link');
    }

    /**
     * Общий метод для извлечения рейтингов
     *
     * A General Method for Rankings Extraction.
     *
     * Этот метод используется для извлечения данных рейтингов с помощью указанного CSS селектора.
     * Метод отправляет HTTP запрос по указанному URL, извлекает данные с помощью DOM парсера
     * и формирует итоговый список рейтингов с их id, slug и именами.
     *
     * This method is used to extract ranking data using the specified CSS selector.
     * It sends an HTTP request to the provided URL, extracts the data using the DOM parser,
     * and forms a final list of rankings with their id, slug, and names.
     */
    protected function crawlRankingData(string $selector): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.genres_url');

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $rankingItems = $crawler->filter($selector)->eq(5);

        $idCounter = 1;

        $rankings = $rankingItems->filter('.genre-name-link')->each(function ($node) use (&$idCounter) {
            $name = $node->text();
            $slug = isset($name) ? $this->generateSlug($name) : null;

            return [
                'id' => $idCounter++,
                'slug' => $slug,
                'name' => $name,
            ];
        });

        $rankings = array_filter($rankings);

        return response()->json(array_values($rankings));
    }
}
