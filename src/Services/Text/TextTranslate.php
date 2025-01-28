<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Services\Text;

use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;

class TextTranslate extends BaseService
{
    /**
     * Получить студии с указанного URL
     *
     * Get studios from the specified URL.
     *
     * Этот метод используется для получения списка студий с указанного URL.
     * Он вызывает `crawlStudioData` с заранее определённым CSS селектором.
     *
     * This method is used to get studios from the specified URL.
     * It calls `crawlStudioData` with a predefined CSS selector.
     *
     * @return JsonResponse JSON объект с студиями
     */
    public function crawlStudios(): JsonResponse
    {
        return $this->crawlStudioData('.anime-manga-search .genre-link');
    }

    /**
     * Получить информацию о студии по её malId
     *
     * Get description of a studio by malId.
     *
     * Этот метод используется для получения подробной информации о студии по её `malId`.
     * Метод отправляет HTTP запрос по указанному URL, извлекает данные о студии,
     * и формирует структуру с её названием, изображением, дополнительной информацией и описанием.
     *
     * This method is used to get detailed information about a studio by its `malId`.
     * It sends an HTTP request to the provided URL, extracts studio details,
     * and forms a structure with the studio's name, image, additional information, and description.
     */
    public function crawlStudioInformation(int $malId): JsonResponse
    {
        $baseUrl   = config('malCrawler.base_url');
        $genresUrl = config('malCrawler.studio_url');
        $url       = $baseUrl.$genresUrl.'/'.$malId;

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $title = $crawler->filter('.title-name')->count() > 0
          ? $crawler->filter('.title-name')->text()
          : null;

        $slug = $this->generateSlug($title);

        $image = $crawler->filter('#content .content-left .logo img')->count() > 0
          ? $crawler->filter('#content .content-left .logo img')
          : null;

        $imageSrc = $image?->attr('data-src') ?? $image?->attr('src') ?? 'https://cdn.myanimelist.net/images/company_no_picture.png';

        if ($imageSrc === config('malCrawler.no_studio_picture')) {
            $imageSrc = null;
        }

        $imageAlt = $image?->attr('alt');

        $infoNode = $crawler->filter('#content .content-left .mb16')->eq(1);

        $info = [];

        if ($infoNode->count() > 0) {
            $infoNode->filter('.spaceit_pad')->each(function ($item) use (&$info) {
                $label = $item->filter('span.dark_text')->count() > 0
                  ? trim($item->filter('span.dark_text')->text(), ':')
                  : null;

                $value = $item->filter('span.dark_text')->count() > 0
                  ? trim(str_replace($item->filter('span.dark_text')->text(), '', $item->text()))
                  : $item->text();

                if ($label) {
                    $info[strtolower(str_replace(' ', '_', $label))] = trim($value);
                }
            });
        }

        $japanese = $info['japanese'] ?? null;
        if ($japanese) {
            $info['japanese'] = $this->decodeUnicode($japanese);
        }

        $description = null;
        $crawler->filter('#content .content-left .mb16')->eq(1)->filter('.spaceit_pad')->each(function ($item) use (&$description) {
            $text = trim($item->text());

            if ($item->filter('span.dark_text')->count() === 0 && strlen($text) > 10) {
                $description = $text;
            }
        });

        $info['description'] = $description ?? null;

        return response()->json([
            'malId' => $malId,
            'slug' => $slug,
            'title' => $title,
            'image' => [
                'src' => $imageSrc,
                'alt' => $imageAlt,
            ],
            'info' => $info,
        ]);
    }

    /**
     * Общий метод для извлечения студий
     *
     * A General Method for Studio Extraction.
     *
     * Этот метод используется для извлечения данных о студиях с помощью указанного CSS селектора.
     * Метод отправляет HTTP запрос по указанному URL, извлекает данные с помощью DOM парсера,
     * и формирует итоговый список студий с их id, malId, названием, количеством титулов и ссылкой.
     *
     * This method is used to extract studio data using the specified CSS selector.
     * It sends an HTTP request to the provided URL, extracts the data using the DOM parser,
     * and forms a final list of studios with their id, malId, name, titles count, and link.
     */
    protected function crawlStudioData(string $selector): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.studio_url');

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $studioItems = $crawler->filter($selector);

        $idCounter = 1;

        $studios = $studioItems->filter('.genre-name-link')->each(function ($node) use (&$idCounter) {

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

        $studios = array_filter($studios);

        return response()->json(array_values($studios));
    }
}
