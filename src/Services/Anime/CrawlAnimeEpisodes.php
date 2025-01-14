<?php

namespace AniMik\MalCrawler\Services\Anime;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnimeEpisodes extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Основной метод для получения списка эпизодов аниме.
     * Принимает MAL ID аниме, отправляет запрос на сайт и возвращает данные о эпизодах в формате JSON.
     *
     * Main method for retrieving the anime episodes list.
     * Accepts the anime's MAL ID, sends a request to the site, and returns episode data in JSON format.
     */
    public function crawlAnimeEpisodesList(int $malId): JsonResponse
    {
        return $this->getAnimeEpisodesListData($malId);
    }

    /**
     * Получает данные о списке эпизодов аниме.
     * Формирует URL, отправляет запрос, парсит контент страницы и возвращает данные о эпизодах.
     *
     * Retrieves the anime episodes list data.
     * Constructs the URL, sends the request, parses the page content, and returns episode data.
     */
    private function getAnimeEpisodesListData(int $malId): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.anime_url').'/'.$malId.'/_/episode';

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([
                'malId' => $malId,
                'error' => 'Anime episodes Not Found',
            ]);
        }

        $crawler = new Crawler($content);

        $getEpisodes = $this->getEpisodes($crawler);

        return response()->json($getEpisodes);
    }

    /**
     * Извлекает данные о эпизодах аниме из HTML-страницы.
     * Перебирает строки таблицы с эпизодами и собирает информацию.
     *
     * Extracts anime episodes data from the HTML page.
     * Iterates through the table rows with episodes and collects information.
     */
    private function getEpisodes(Crawler $crawler): array
    {
        return $crawler
            ->filterXPath('//table[contains(@class, "episode_list")]/tbody/tr')
            ->each(
                function (Crawler $crawler) {
                    $id = $this->getEpisodeId($crawler);
                    $url = $this->getEpisodeUrl($crawler);
                    $title = $this->getTitle($crawler);
                    $japanese = $this->getTitleJapanese($crawler);
                    $romanji = $this->getTitleRomanji($crawler);
                    $aired = $this->getAired($crawler);
                    $score = $this->getScore($crawler);
                    $videoUrl = $this->getVideoUrl($crawler);

                    return [
                        'id' => $id,
                        'url' => $url,
                        'title' => $title,
                        'japanese' => $japanese,
                        'romanji' => $romanji,
                        'aired' => $aired,
                        'score' => $score,
                        'videoUrl' => $videoUrl,
                    ];
                }
            );
    }

    /**
     * Извлекает ID эпизода из строки таблицы.
     * Находит номер эпизода в DOM-структуре и возвращает его как целое число.
     *
     * Extracts the episode ID from a table row.
     * Finds the episode number in the DOM structure and returns it as an integer.
     */
    public function getEpisodeId(Crawler $crawler): int
    {
        return (int) $crawler->filterXPath('//td[contains(@class, \'episode-number\')]')->text();
    }

    public function getEpisodeUrl(Crawler $crawler): string
    {
        return $crawler->filterXPath('//td[contains(@class,"episode-title")]/a')->attr('href');
    }

    public function getTitle(Crawler $crawler): string
    {
        return $crawler->filterXPath('//td[contains(@class, "episode-title")]/a')->text();
    }

    public function getTitleJapanese(Crawler $crawler): ?string
    {
        $title = $crawler->filterXPath('//td[contains(@class, "episode-title")]/span[@class=\'di-ib\']')->text();

        if (empty($title)) {
            return null;
        }

        preg_match('~(.*)\((.*)\)~', $title, $matches);

        return ! empty($matches[2]) ? $matches[2] : null;
    }

    public function getTitleRomanji(Crawler $crawler): ?string
    {
        $title = $crawler->filterXPath('//td[contains(@class, "episode-title")]/span[@class=\'di-ib\']')->text();

        if (empty($title)) {
            return null;
        }

        preg_match('~(.*)\((.*)\)~', $title, $matches);

        return ! empty($matches[1]) ? $matches[1] : null;
    }

    public function getAired(Crawler $crawler): ?string
    {
        $aired = $crawler->filterXPath('//td[contains(@class, \'episode-aired\')]')->text();

        if ($aired === 'N/A') {
            return null;
        }

        return $aired;
    }

    public function getScore(Crawler $crawler): ?float
    {
        $node = $crawler
            ->filterXPath('//td[contains(@class, \'episode-poll\')]/@data-raw');

        if (! $node->count()) {
            return null;
        }

        $score = $node->text();

        if ($score) {
            return null;
        }

        return (float) $score;
    }

    public function getVideoUrl(Crawler $crawler): ?string
    {
        $video = $crawler->filterXPath('//td[contains(@class, \'episode-video\')]/a');

        if (! $video->count()) {
            return null;
        }

        return $video->attr('href');
    }
}
