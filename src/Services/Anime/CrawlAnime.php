<?php

namespace AniMik\MalCrawler\Services\Anime;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlAnime extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Краулит информацию об аниме по его идентификатору на MyAnimeList (MAL).
     * Этот метод извлекает подробные данные о конкретном аниме
     *
     * Crawls detailed information about a specific anime based on its MyAnimeList (MAL) ID.
     * This method retrieves comprehensive data about the anime
     */
    public function crawlAnime(int $malId): JsonResponse
    {
        return $this->getAnimeData($malId);
    }

    /**
     * Вернуть все валидные ID в формате JSON
     *
     * Returns all valid anime IDs in JSON format.
     *
     * Этот метод использует метод findAllValidAnimeIds для нахождения валидных ID,
     * а затем возвращает их в формате JSON.
     *
     * This method uses the findAllValidAnimeIds method to find valid IDs,
     * and then returns them in JSON format.
     *
     * @throws TransportExceptionInterface
     */
    public function crawlValidAnimeIds(): JsonResponse
    {
        $ids = $this->findAllValidAnimeIds();

        return response()->json([
            'ids' => $ids,
        ]);
    }

    /**
     * Находит все действительные ID аниме на MyAnimeList
     *
     * Finds all valid anime IDs on MyAnimeList.
     *
     * Этот метод перебирает диапазон ID от $low до $high, проверяя, какие из них существуют на сайте MyAnimeList.
     * Для каждого ID отправляется GET-запрос, и если статус ответа равен 200, ID добавляется в список действительных ID.
     * Метод использует пакетную обработку, чтобы обрабатывать ID в блоках, размеры которых определяются параметром batch_size.
     * В конце метода список обработанных ID сохраняется, и пауза между запросами регулируется параметром sleep_interval из конфигурации.
     *
     * This method iterates through a range of IDs from $low to $high, checking which ones are valid on MyAnimeList.
     * For each ID, a GET request is sent, and if the status code is 200, the ID is added to the list of valid IDs.
     * The method processes IDs in batches, with the batch size defined by the batch_size configuration parameter.
     * At the end of the method, the list of processed IDs is saved, and a delay between requests is controlled by the sleep_interval configuration parameter.
     *
     * @throws TransportExceptionInterface
     */
    private function findAllValidAnimeIds(): array
    {
        $low = 1;
        $high = config('malCrawler.last_anime_id');
        $baseUrl = config('malCrawler.base_url');
        $animeUrl = config('malCrawler.anime_url');
        $url = rtrim($baseUrl, '/').$animeUrl.'/';

        $processedIds = $this->getProcessedIds();

        $validIds = [];
        $batchSize = config('malCrawler.batch_size');

        for ($i = $low; $i <= $high; $i += $batchSize) {
            $batchIds = [];

            for ($j = $i; $j < $i + $batchSize && $j <= $high; $j++) {
                if (in_array($j, $processedIds)) {
                    continue;
                }

                $response = $this->httpClient->request('GET', $url.$j);

                if ($response->getStatusCode() === 200) {
                    $batchIds[] = $j;
                }
            }

            $validIds = array_merge($validIds, $batchIds);

            $processedIds = array_merge($processedIds, $batchIds);
            $this->saveProcessedIds($processedIds);

            sleep(config('malCrawler.sleep_interval'));
        }

        return array_merge($validIds, $processedIds);
    }

    /**
     * Получает список уже обработанных ID из кеш-файла.
     * Если файл существует, считывает его содержимое, декодирует JSON и возвращает массив с ID.
     * Если файл не существует или данные в файле не могут быть декодированы, возвращает пустой массив.
     *
     * Retrieves the list of already processed IDs from the cache file.
     * If the file exists, reads its contents, decodes the JSON, and returns an array of IDs.
     * If the file does not exist or the data cannot be decoded, returns an empty array.
     */
    private function getProcessedIds(): array
    {
        $processedIdsFile = __DIR__.'/../../../storage/Cache/ids.json';

        if (file_exists($processedIdsFile)) {
            $json = file_get_contents($processedIdsFile);

            return json_decode($json, true) ?: [];
        }

        return [];
    }

    /**
     * Сохраняет список обработанных ID в кеш-файл.
     * Преобразует массив ID в формат JSON с отступами и сохраняет его в файл.
     * Если файл не существует, он будет создан.
     *
     * Saves the list of processed IDs to the cache file.
     * Converts the array of IDs into a JSON format with indentation and writes it to the file.
     * If the file does not exist, it will be created.
     */
    private function saveProcessedIds(array $processedIds): void
    {
        $processedIdsFile = __DIR__.'/../../../storage/Cache/ids.json';

        $json = json_encode($processedIds, JSON_PRETTY_PRINT);
        file_put_contents($processedIdsFile, $json);
    }

    /**
     * Выполняет парсинг данных о аниме с указанным ID на MyAnimeList.
     * Отправляет HTTP-запрос, обрабатывает ошибки, извлекает различные данные о аниме, такие как название, жанры, описание, рейтинг и другие.
     * Возвращает все собранные данные в формате JSON.
     *
     * Parses anime data for a given ID from MyAnimeList.
     * Sends an HTTP request, handles errors, extracts various anime details like title, genres, synopsis, rating, and others.
     * Returns all gathered data in JSON format.
     */
    private function getAnimeData(int $malId): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.anime_url').'/'.$malId.'_/';

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([
                'malId' => $malId,
                'error' => 'Anime Not Found',
            ]);
        }

        $crawler = new Crawler($content);

        $titleUrl = $this->getURL($crawler);
        $synopsis = $this->getSynopsis($crawler);
        $slug = $this->getSlug($titleUrl);
        $english = $this->getTitleEnglish($crawler);
        $japanese = $this->getTitleJapanese($crawler);
        $synonyms = $this->getTitleSynonyms($crawler);
        $title = $this->getTitle($crawler);
        $type = $this->getType($crawler);
        $status = $this->getStatus($crawler);
        $ImageUrl = $this->getImageURL($crawler);
        $episodes = $this->getEpisodes($crawler);
        $premiered = $this->getPremiered($crawler);
        $broadcast = $this->getBroadcast($crawler);
        $producers = $this->getProducers($crawler);
        $licensors = $this->getLicensors($crawler);
        $studios = $this->getStudios($crawler);
        $source = $this->getSource($crawler);
        $genres = $this->getGenres($crawler);
        $explicitGenres = $this->getExplicitGenres($crawler);
        $getDemographics = $this->getDemographics($crawler);
        $getThemes = $this->getThemes($crawler);
        $getDuration = $this->getDuration($crawler);
        $getScoredBy = $this->getScoredBy($crawler);
        $getScore = $this->getScore($crawler);
        $getRating = $this->getRating($crawler);
        $getRank = $this->getRank($crawler);
        $getPopularity = $this->getPopularity($crawler);
        $getMembers = $this->getMembers($crawler);
        $getFavorites = $this->getFavorites($crawler);
        $getExternalLinks = $this->getExternalLinks($crawler);
        $getAired = $this->getAired($crawler);
        $getRelated = $this->getRelated($crawler);

        return response()->json([
            'id' => $malId,
            'slug' => $slug,
            'name' => $title,
            'english' => $english,
            'japanese' => $japanese,
            'synonyms' => $synonyms,
            'image' => $ImageUrl,
            'synopsis' => $synopsis,
            'malUrl' => $titleUrl,
            'episodes' => $episodes,
            'type' => $type,
            'status' => $status,
            'premiered' => $premiered,
            'broadcast' => $broadcast,
            'producers' => $producers,
            'licensors' => $licensors,
            'studios' => $studios,
            'source' => $source,
            'genres' => $genres,
            'explicitGenres' => $explicitGenres,
            'demographics' => $getDemographics,
            'themes' => $getThemes,
            'duration' => $getDuration,
            'score' => $getScore,
            'scoredBy' => $getScoredBy,
            'rating' => $getRating,
            'rank' => $getRank,
            'popularity' => $getPopularity,
            'related' => $getRelated,
            'members' => $getMembers,
            'favorites' => $getFavorites,
            'externalLinks' => $getExternalLinks,
            'aired' => $getAired,
        ]);
    }

    /**
     * Извлекает информацию о премьере аниме.
     * Ищет текст "Premiered:" на странице и извлекает дату премьеры из ближайшего родительского элемента.
     * Если дата не найдена или значение равно "?", возвращает null.
     *
     * Extracts the premiere information of the anime.
     * Searches for the text "Premiered:" on the page and extracts the premiere date from the nearest ancestor element.
     * If the date is not found or the value is "?", it returns null.
     */
    private function getPremiered(Crawler $crawler): ?string
    {
        $premiered = $crawler->filterXPath('//span[text()="Premiered:"]');

        if (! $premiered->count()) {
            return null;
        }

        $premiered = trim(str_replace($premiered->text(), '', $premiered->ancestors()->text()));

        if ($premiered === '?') {
            return null;
        }

        return $premiered;
    }

    /**
     * Извлекает связанные элементы (аниме, мангу, фильмы и т.д.) с текущей страницы.
     * Метод обрабатывает два типа элементов: "related-entries" и "entries-table", извлекая информацию о связанных записях.
     * Каждый элемент связан с его названием, ссылкой и ID.
     *
     * Retrieves related items (anime, manga, movies, etc.) from the current page.
     * The method processes two types of elements: "related-entries" and "entries-table", retrieving information about related entries.
     * Each element is associated with its name, link, and ID.
     */
    public function getRelated(Crawler $crawler): array
    {
        $related = [];

        $crawler->filterXPath('//div[contains(@class, "related-entries")]/div[contains(@class, "entries-tile")]/div[contains(@class, "entry")]')->each(
            function (Crawler $c) use (&$related) {
                $relation = $c->filterXPath('//div[@class="content"]/div[@class="relation"]');

                if (! $relation->count()) {
                    return;
                }

                $relation = trim(preg_replace("~\s\(.*\)~", '', $relation->text()));

                $links = $c->filterXPath('//div[@class="content"]/div[@class="title"]/a');

                if ($links->count() === 1 && empty($links->first()->text())) {
                    return;
                }

                $related[$relation] = $this->extractRelatedData($links);
            }
        );

        $crawler->filterXPath('//table[contains(@class, "entries-table")]/tr')->each(
            function (Crawler $c) use (&$related) {
                $links = $c->filterXPath('//td[2]//a');
                $relation = trim(str_replace(':', '', $c->filterXPath('//td[1]')->text()));

                if ($links->count() === 1 && empty($links->first()->text())) {
                    $related[$relation] = [];

                    return;
                }

                $related[$relation] = $this->extractRelatedData($links);
            }
        );

        return $related;
    }

    /**
     * Извлекает данные о связанных элементах (ID, название, URL) из списка ссылок.
     *
     * Extracts data about related items (ID, title, URL) from a list of links.
     */
    private function extractRelatedData(Crawler $links): array
    {
        return $links->each(function (Crawler $crawler) {
            $url = $crawler->attr('href');
            $id = $this->extractIdFromUrl($url);
            $title = $crawler->text();
            $slug = $this->getSlug($title);

            return [
                'id' => $id,
                'slug' => $slug,
                'title' => $title,
                'url' => $url,
            ];
        });
    }

    /**
     * Извлекает ID из URL.
     * Находит числовой ID в URL-строке и возвращает его как целое число.
     * Если ID не найден, возвращает null.
     *
     * Extracts an ID from a URL.
     * Finds a numeric ID in a URL string and returns it as an integer.
     * f the ID is not found, returns null.
     */
    public function extractIdFromUrl(string $url): ?int
    {
        if (preg_match('/\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Извлекает источник аниме.
     * Ищет текст "Source:" на странице и извлекает информацию о источнике из ближайшего родительского элемента.
     * Если источник не найден, возвращает null.
     *
     * Extracts the source information of the anime.
     * Searches for the text "Source:" on the page and extracts the source information from the nearest ancestor element.
     * If the source is not found, it returns null.
     */
    private function getSource(Crawler $crawler): ?string
    {
        $source = $crawler
            ->filterXPath('//span[text()="Source:"]');

        if (! $source->count()) {
            return null;
        }

        return trim(str_replace($source->text(), '', $source->ancestors()->text()));
    }

    /**
     * Извлекает информацию о популярности аниме.
     * Ищет текст "Popularity:" на странице и извлекает значение популярности из ближайшего родительского элемента.
     * Если значение не найдено, возвращает null.
     *
     * Extracts the popularity information of the anime.
     * Searches for the text "Popularity:" on the page and extracts the popularity value from the nearest ancestor element.
     * If the value is not found, it returns null.
     */
    private function getPopularity(Crawler $crawler): ?int
    {
        $popularity = $crawler
            ->filterXPath('//span[text()="Popularity:"]');

        if (! $popularity->count()) {
            return null;
        }

        return trim(
            str_replace([$popularity->text(), '#'], '', $popularity->ancestors()->text())
        );
    }

    /**
     * Извлекает информацию о количестве участников (членов) аниме.
     * Ищет текст "Members:" на странице и извлекает количество участников из ближайшего родительского элемента.
     * Если количество участников не найдено, возвращает null.
     *
     * Extracts the information about the number of members for the anime.
     * Searches for the text "Members:" on the page and extracts the number of members from the nearest ancestor element.
     * If the number of members is not found, it returns null.
     */
    private function getMembers(Crawler $crawler): ?int
    {
        $member = $crawler
            ->filterXPath('//span[text()="Members:"]');

        if (! $member->count()) {
            return null;
        }

        return trim(
            str_replace([$member->text(), ','], '', $member->ancestors()->text())
        );
    }

    /**
     * Извлекает информацию о количестве избранных аниме.
     * Ищет текст "Favorites:" на странице и извлекает количество избранных из ближайшего родительского элемента.
     * Если количество избранных не найдено, возвращает null.
     *
     * Extracts the information about the number of favorites for the anime.
     * Searches for the text "Favorites:" on the page and extracts the number of favorites from the nearest ancestor element.
     * If the number of favorites is not found, it returns null.
     */
    private function getFavorites(Crawler $crawler): ?int
    {
        $favorite = $crawler
            ->filterXPath('//span[text()="Favorites:"]');

        if (! $favorite->count()) {
            return null;
        }

        return trim(
            str_replace([$favorite->text(), ','], '', $favorite->ancestors()->text())
        );
    }

    /**
     * Извлекает все внешние ссылки на странице.
     * Ищет все ссылки в разделе "external_links", игнорируя ссылки с классом "js-more-links".
     * Если внешние ссылки не найдены, возвращает пустой массив.
     *
     * Extracts all external links on the page.
     * Searches for all links in the "external_links" section, ignoring links with the class "js-more-links".
     * If no external links are found, it returns an empty array.
     */
    private function getExternalLinks(Crawler $crawler): array
    {
        $links = $crawler
            ->filterXPath('//*[@id="content"]/table//div[contains(@class, "external_links")]//a[contains(@class, "link") and not(contains(@class, "js-more-links"))]');

        if (! $links->count()) {
            return [];
        }

        return $links
            ->each(function (Crawler $crawler) {
                return $crawler->attr('href');
            });
    }

    /**
     * Получает дату выхода аниме.
     * Вызывает метод getAnimeAiredString для получения строки с датой выхода.
     *
     * Retrieves the aired date of the anime.
     * Calls the getAnimeAiredString method to extract the aired date string.
     */
    private function getAired(Crawler $crawler): ?string
    {
        return $this->getAnimeAiredString($crawler);
    }

    /**
     * Извлекает строку с датой выхода аниме.
     * Ищет элемент с текстом "Aired", извлекает его HTML-контент и парсит строку с датой.
     *
     * Extracts the aired date string for the anime.
     * Searches for the "Aired" element, extracts its HTML content, and parses the aired date string.
     */
    private function getAnimeAiredString(Crawler $crawler): ?string
    {
        $aired = $crawler->filterXPath('//span[contains(text(), "Aired")]/..')->html();
        $aired = explode("\n", trim($aired))[1];

        return trim($aired);
    }

    /**
     * Извлекает жанры аниме.
     * Ищет элементы с текстом "Genres:" или "Genre:", извлекает жанры, если они есть.
     * Если жанры не добавлены, возвращает пустой массив.
     *
     * Extracts the genres of the anime.
     * Searches for elements with the text "Genres:" or "Genre:", extracts the genres if available.
     * If no genres are found, it returns an empty array.
     */
    private function getGenres(Crawler $crawler): array
    {
        $genre = $crawler
            ->filterXPath('//span[text()="Genres:"]');

        if ($genre->count() && ! str_contains($genre->ancestors()->text(), 'No genres have been added yet')) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        $genre = $crawler
            ->filterXPath('//span[text()="Genre:"]');

        if ($genre->count() && ! str_contains($genre->ancestors()->text(), 'No genres have been added yet')) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает темы аниме.
     * Ищет элементы с текстом "Theme:" или "Themes:", извлекает темы, если они есть.
     * Если темы не найдены, возвращает пустой массив.
     *
     * Extracts the themes of the anime.
     * Searches for elements with the text "Theme:" or "Themes:", extracts the themes if available.
     * If no themes are found, it returns an empty array.
     */
    private function getThemes(Crawler $crawler): array
    {
        $genre = $crawler
            ->filterXPath('//span[text()="Theme:"]');

        if ($genre->count()) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        $genre = $crawler
            ->filterXPath('//span[text()="Themes:"]');

        if ($genre->count()) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает явные жанры аниме.
     * Ищет элементы с текстом "Explicit Genres:" или "Explicit Genre:", извлекает жанры, если они есть.
     * Если явные жанры не добавлены, возвращает пустой массив.
     *
     * Extracts the explicit genres of the anime.
     * Searches for elements with the text "Explicit Genres:" or "Explicit Genre:", extracts the genres if available.
     * If no explicit genres are found, it returns an empty array.
     */
    private function getExplicitGenres(Crawler $crawler): array
    {
        $genre = $crawler
            ->filterXPath('//span[text()="Explicit Genres:"]');

        if ($genre->count() && ! str_contains($genre->ancestors()->text(), 'No genres have been added yet')) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        $genre = $crawler
            ->filterXPath('//span[text()="Explicit Genre:"]');

        if ($genre->count() && ! str_contains($genre->ancestors()->text(), 'No genres have been added yet')) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает продолжительность аниме.
     * Ищет элемент с текстом "Duration:", извлекает продолжительность, если она есть.
     * Если продолжительность не найдена, возвращает null.
     *
     * Extracts the duration of the anime.
     * Searches for the "Duration:" element, extracts the duration if available.
     * If the duration is not found, it returns null.
     */
    private function getDuration(Crawler $crawler): ?string
    {
        $duration = $crawler
            ->filterXPath('//span[text()="Duration:"]');

        if (! $duration->count() || $duration->text() === 'Unknown') {
            return null;
        }

        return trim(str_replace(
            '.',
            '',
            str_replace($duration->text(), '', $duration->ancestors()->text())));
    }

    /**
     * Извлекает количество пользователей, оценивших аниме.
     * Ищет элемент с атрибутом "itemprop" равным "ratingCount", извлекает количество оценок.
     * Если данные не найдены или значение не является числом, возвращает null.
     *
     * Extracts the number of users who scored the anime.
     * Searches for the element with the "itemprop" attribute equal to "ratingCount", extracts the rating count.
     * If the data is not found or the value is not numeric, it returns null.
     */
    private function getScoredBy(Crawler $crawler): ?int
    {
        $scoredBy = $crawler->filterXPath('//span[@itemprop="ratingCount"]');

        if (! $scoredBy->count()) {
            return null;
        }

        $scoredBy = trim($scoredBy->text());

        $scoredByNum = str_replace(
            [',', ' users', ' user'],
            '',
            $scoredBy
        );

        if (! is_numeric($scoredByNum)) {
            return null;
        }

        return (int) $scoredByNum;
    }

    /**
     * Извлекает ранг аниме.
     * Ищет элемент с текстом "Ranked:", извлекает ранг, если он есть.
     * Если ранг не найден или равен "N/A", возвращает null.
     *
     * Extracts the rank of the anime.
     * Searches for the "Ranked:" element, extracts the rank if available.
     * If the rank is not found or is equal to "N/A", it returns null.
     */
    private function getRank(Crawler $crawler): ?string
    {
        $rank = $crawler
            ->filterXPath('//span[text()="Ranked:"]');

        if (! $rank->count()) {
            return null;
        }

        $ranked = trim(
            $rank->ancestors()->text()
        );

        if ($ranked === 'N/A') {
            return null;
        }

        return trim(str_replace(
            '#',
            '',
            $ranked
        ));
    }

    /**
     * Извлекает рейтинг аниме.
     * Ищет элемент с атрибутом "itemprop" равным "ratingValue", извлекает значение рейтинга.
     * Если рейтинг не найден или равен "N/A", возвращает null.
     *
     * Extracts the score of the anime.
     * Searches for the element with the "itemprop" attribute equal to "ratingValue", extracts the score value.
     * If the score is not found or is equal to "N/A", it returns null.
     */
    private function getScore(Crawler $crawler): ?float
    {
        $score = $crawler->filterXPath('//span[@itemprop="ratingValue"]');

        if (! $score->count()) {
            return null;
        }

        $score = trim($score->text());

        if ($score === 'N/A') {
            return null;
        }

        return (float) $score;
    }

    /**
     * Извлекает рейтинг аниме (цензура).
     * Ищет элемент с текстом "Rating:", извлекает значение рейтинга.
     * Если рейтинг не найден или равен "None", возвращает null.
     *
     * Extracts the rating of the anime (censorship rating).
     * Searches for the "Rating:" element, extracts the rating value.
     * If the rating is not found or is equal to "None", it returns null.
     */
    private function getRating(Crawler $crawler): ?string
    {
        $rating = $crawler
            ->filterXPath('//span[text()="Rating:"]');

        if (! $rating->count()) {
            return null;
        }

        $rating = trim(str_replace($rating->text(), '', $rating->ancestors()->text()));

        if ($rating === 'None') {
            return null;
        }

        return $rating;
    }

    /**
     * Извлекает демографическую информацию аниме.
     * Ищет элементы с текстом "Demographic:" или "Demographics:",
     * извлекает ссылки, у которых в атрибуте 'title' содержится информация о демографической группе.
     * Если демографическая информация не найдена, возвращает пустой массив.
     *
     * Extracts the demographic information of the anime.
     * Searches for elements with the text "Demographic:" or "Demographics:",
     * extracts links that have the 'title' attribute containing information about the demographic group.
     * If no demographic information is found, returns an empty array.
     */
    private function getDemographics(Crawler $crawler): array
    {
        $genre = $crawler
            ->filterXPath('//span[text()="Demographic:"]');

        if ($genre->count()) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        $genre = $crawler
            ->filterXPath('//span[text()="Demographics:"]');

        if ($genre->count()) {
            return $genre->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает студии, участвующие в создании аниме.
     * Ищет элемент с текстом "Studios:", извлекает ссылки на студии.
     * Если не найдены студии или они указаны как "None found", возвращает пустой массив.
     *
     * Extracts the studios involved in the creation of the anime.
     * Searches for the "Studios:" element and extracts the studio links.
     * If no studios are found or they are marked as "None found", returns an empty array.
     */
    private function getStudios(Crawler $crawler): array
    {
        $studio = $crawler->filterXPath('//span[text()="Studios:"]');

        if ($studio->count() && ! str_contains($studio->ancestors()->text(), 'None found')) {
            return $studio->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает информацию о лицензирах аниме.
     * Ищет элемент с текстом "Licensors:", извлекает ссылки на лицензиаров.
     * Если лицензии не найдены или они указаны как "None found", возвращает пустой массив.
     *
     * Extracts the licensors of the anime.
     * Searches for the "Licensors:" element and extracts the licensor links.
     * If no licensors are found or they are marked as "None found", returns an empty array.
     */
    private function getLicensors(Crawler $crawler): array
    {
        $licensor = $crawler->filterXPath('//span[text()="Licensors:"]');

        if ($licensor->count() && ! str_contains($licensor->ancestors()->text(), 'None found')) {
            return $licensor->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает информацию о продюсерах аниме.
     * Ищет элемент с текстом "Producers:", извлекает ссылки на продюсеров.
     * Если продюсеры не найдены или они указаны как "None found", возвращает пустой массив.
     *
     * Extracts the producers of the anime.
     * Searches for the "Producers:" element and extracts the producer links.
     * If no producers are found or they are marked as "None found", returns an empty array.
     */
    private function getProducers(Crawler $crawler): array
    {
        $producer = $crawler
            ->filterXPath('//span[text()="Producers:"]');

        if ($producer->count() && ! str_contains($producer->ancestors()->text(), 'None found')) {
            return $producer->ancestors()->first()->filterXPath('//a')->each(
                function (Crawler $crawler) {
                    return $crawler->attr('title');
                }
            );
        }

        return [];
    }

    /**
     * Извлекает информацию о трансляции аниме.
     * Ищет элемент с текстом "Broadcast:", и извлекает текст, который идет после этого элемента.
     * Если трансляция не указана, возвращает null.
     *
     * Extracts information about the anime's broadcast.
     * Searches for the "Broadcast:" element and extracts the text that follows it.
     * If no broadcast information is found, returns null.
     */
    private function getBroadcast(Crawler $crawler): ?string
    {
        $broadcast = $crawler
            ->filterXPath('//span[text()="Broadcast:"]');

        if (! $broadcast->count()) {
            return null;
        }

        return trim(str_replace($broadcast->text(), '', $broadcast->ancestors()->text()));
    }

    /**
     * Извлекает количество эпизодов аниме.
     * Ищет элемент с текстом "Episodes:", извлекает количество эпизодов.
     * Если количество эпизодов указано как "Unknown" или не найдено, возвращает null.
     *
     * Extracts the number of episodes of the anime.
     * Searches for the "Episodes:" element and extracts the number of episodes.
     * If the number of episodes is marked as "Unknown" or not found, returns null.
     */
    private function getEpisodes(Crawler $crawler): ?int
    {
        $episodes = $crawler->filterXPath('//span[text()="Episodes:"]');

        if (! $episodes->count()) {
            return null;
        }

        return
          (
              trim(
                  str_replace($episodes->text(), '', $episodes->ancestors()->text())
              ) === 'Unknown'
          )
            ?
            null
            :
            (int) str_replace(
                $episodes->text(),
                '',
                $episodes->ancestors()->text()
            );
    }

    /**
     * Извлекает URL страницы аниме.
     * Ищет мета-тег с атрибутом 'og:url' и извлекает значение атрибута 'content'.
     *
     * Extracts the URL of the anime's page.
     * Searches for the meta tag with the 'og:url' attribute and extracts the value of its 'content' attribute.
     */
    private function getURL(Crawler $crawler): string
    {
        return $crawler->filterXPath('//meta[@property=\'og:url\']')->attr('content');
    }

    /**
     * Извлекает "slug" из URL аниме.
     * Преобразует последний сегмент пути URL в "slug", который является строкой, используемой для создания уникального идентификатора.
     *
     * Extracts the "slug" from the anime's URL.
     * Converts the last segment of the URL path into a "slug", which is a string used to create a unique identifier.
     */
    private function getSlug(string $titleUrl): string
    {
        $slug = parse_url($titleUrl);

        return $this->generateSlug(basename($slug['path']));
    }

    /**
     * Извлекает синопсис аниме.
     * Ищет элемент с атрибутом 'itemprop' равным 'description' и извлекает его HTML содержимое.
     * Если синопсис начинается с фразы "No synopsis information has been added to this title.", возвращает null.
     *
     * Extracts the synopsis of the anime.
     * Searches for the element with the 'itemprop' attribute set to 'description' and extracts its HTML content.
     * If the synopsis starts with "No synopsis information has been added to this title.", returns null.
     */
    private function getSynopsis(Crawler $crawler): ?string
    {
        $synopsis = $crawler->filterXPath('//p[@itemprop=\'description\']')->html();

        return str_starts_with($synopsis, 'No synopsis information has been added to this title.') ? null : $synopsis;
    }

    /**
     * Извлекает английское название аниме.
     * Ищет элемент с текстом "English:", извлекает название, которое идет после этого элемента.
     * Если английское название не найдено, возвращает null.
     *
     * Extracts the English title of the anime.
     * Searches for the "English:" element and extracts the title that follows it.
     * If no English title is found, returns null.
     */
    private function getTitleEnglish(Crawler $crawler): ?string
    {
        $title = $crawler->filterXPath('//span[text()="English:"]');
        if (! $title->count()) {
            return null;
        }

        return trim(str_replace($title->text(), '', $title->ancestors()->text()));
    }

    /**
     * Извлекает URL изображения аниме.
     * Ищет мета-тег с атрибутом 'og:image' и извлекает значение атрибута 'content', который содержит URL изображения.
     *
     * Extracts the URL of the anime's image.
     * Searches for the meta tag with the 'og:image' attribute and extracts the value of its 'content' attribute, which contains the image URL.
     */
    private function getImageURL(Crawler $crawler): string
    {
        return $crawler->filterXPath('//meta[@property=\'og:image\']')->attr('content');
    }

    /**
     * Извлекает статус аниме.
     * Ищет элемент с текстом "Status:", извлекает статус, который идет после этого элемента.
     * Если статус не найден, возвращает null.
     *
     * Extracts the status of the anime.
     * Searches for the "Status:" element and extracts the status that follows it.
     * If no status is found, returns null.
     */
    private function getStatus(Crawler $crawler): ?string
    {
        $status = $crawler->filterXPath('//span[text()="Status:"]');

        if (! $status->count()) {
            return null;
        }

        return trim(str_replace($status->text(), '', $status->ancestors()->text()));
    }

    /**
     * Извлекает тип аниме.
     * Ищет элемент с текстом "Type:", извлекает тип аниме, который идет после этого элемента.
     * Если тип аниме равен "Unknown" или не найден, возвращает null.
     *
     * Extracts the type of the anime.
     * Searches for the "Type:" element and extracts the type of the anime that follows it.
     * If the type is "Unknown" or not found, returns null.
     */
    private function getType(Crawler $crawler): ?string
    {
        $type = $crawler
            ->filterXPath('//span[text()="Type:"]');

        if (! $type->count()) {
            return null;
        }

        $type = trim(str_replace($type->text(), '', $type->ancestors()->text()));

        return $type === 'Unknown' ? null : $type;
    }

    /**
     * Извлекает синонимы аниме.
     * Ищет элемент с текстом "Synonyms:", извлекает синонимы, которые идут после этого элемента.
     * Если синонимы не найдены, возвращает пустой массив.
     *
     * Extracts the synonyms of the anime.
     * Searches for the "Synonyms:" element and extracts the synonyms that follow it.
     * If no synonyms are found, returns an empty array.
     */
    private function getTitleSynonyms(Crawler $crawler): array
    {
        $synonymsElement = $crawler->filterXPath('//span[text()="Synonyms:"]');

        if (! $synonymsElement->count()) {
            return [];
        }

        $synonymsText = $synonymsElement->ancestors()->text();
        $synonymsText = str_replace($synonymsElement->text(), '', $synonymsText);
        $synonyms = explode(', ', $synonymsText);

        foreach ($synonyms as &$synonym) {
            $synonym = trim($synonym);
        }

        return $synonyms;
    }

    /**
     * Извлекает японское название аниме.
     * Ищет элемент с текстом "Japanese:", извлекает название, которое идет после этого элемента.
     * Если японское название не найдено, возвращает null.
     *
     * Extracts the Japanese title of the anime.
     * Searches for the "Japanese:" element and extracts the title that follows it.
     * If no Japanese title is found, returns null.
     */
    private function getTitleJapanese(Crawler $crawler): ?string
    {
        $title = $crawler->filterXPath('//span[text()="Japanese:"]');

        if (! $title->count()) {
            return null;
        }

        return trim(str_replace($title->text(), '', $title->ancestors()->text()));
    }

    /**
     * Извлекает название аниме.
     * Ищет мета-тег с атрибутом 'og:title' и извлекает значение атрибута 'content', который содержит название аниме.
     *
     * Extracts the title of the anime.
     * Searches for the meta tag with the 'og:title' attribute and extracts the value of its 'content' attribute, which contains the anime's title.
     */
    private function getTitle(Crawler $crawler): string
    {
        return $crawler->filterXPath('//meta[@property="og:title"]')->attr('content');
    }
}
