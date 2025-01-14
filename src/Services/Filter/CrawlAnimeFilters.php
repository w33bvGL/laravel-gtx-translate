<?php

namespace AniMik\MalCrawler\Services\Filter;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnimeFilters extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Извлекает и возвращает данные фильтра "Тип"
     *
     * Extracts and returns the "Type" filter data.
     */
    public function crawlTypes(): JsonResponse
    {
        return $this->crawlFilterData(0);
    }

    /**
     * Извлекает и возвращает данные фильтра "Статус"
     *
     * Extracts and returns the "Status" filter data.
     */
    public function crawlStatus(): JsonResponse
    {
        return $this->crawlFilterData(2);
    }

    /**
     * Извлекает и возвращает данные фильтра "Рейтинг"
     *
     * Extracts and returns the "Rated" filter data.
     */
    public function crawlRated(): JsonResponse
    {
        return $this->crawlFilterData(4);
    }

    /**
     * Извлекает и возвращает данные фильтра "Столбцы"
     *
     * Extracts and returns the "Columns" filter data.
     */
    public function crawlColumns(): JsonResponse
    {
        return $this->crawlFilterData(7);
    }

    /**
     * Общий метод для извлечения данных фильтра по индексу строки.
     *
     * A General Method for Filter Data Extraction by Row Index.
     *
     * Этот метод делает HTTP запрос для получения HTML-контента страницы,
     * затем использует библиотеку DomCrawler для извлечения данных из таблицы фильтров.
     * Парсит фильтры на основе переданного индекса строки и возвращает их в формате JSON.
     *
     * This method makes an HTTP request to fetch the HTML content of the page,
     * then uses the DomCrawler library to extract filter data from the table.
     * It parses the filters based on the provided row index and returns them in JSON format.
     *
     * @param  int  $rowIndex  The row index in the table to parse. For example, 0 is "Type", 2 is "Status".
     * @return JsonResponse Возвращает распарсенные опции в формате JSON.
     */
    private function crawlFilterData(int $rowIndex): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.genres_url');
        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([]);
        }

        $crawler = new Crawler($content);

        $filterRow = $crawler->filter('#advancedSearch table')->filter('tr')->eq($rowIndex);

        $idCounter = 1;
        $options = $filterRow->filter('td')->eq(1)->filter('select option')->each(function (Crawler $option) use (&$idCounter) {
            return [
                'id' => $idCounter++,
                'value' => $option->attr('value'),
                'text' => $option->text(),
                // 'selected' => $option->attr('selected') === 'selected',
            ];
        });

        return response()->json(array_values(array_filter($options)));
    }
}
