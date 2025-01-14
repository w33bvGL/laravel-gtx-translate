<?php

namespace AniMik\MalCrawler\Services\Anime;

use AniMik\MalCrawler\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlCharactersAndStaff extends BaseService
{
    protected HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    /**
     * Краулит информацию о персонажах и сотрудниках для всех аниме на основе их идентификатора MyAnimeList (MAL).
     * Этот метод извлекает подробные данные о персонажах и сотрудниках для указанного аниме.
     *
     * Crawls characters and staff information for a specific anime based on its MyAnimeList (MAL) ID.
     * This method retrieves detailed data about characters and staff for the given anime.
     */
    public function crawlAnimeCharactersAndStaff(int $malId): JsonResponse
    {
        return $this->getAnimeCharactersAndStaffData($malId);
    }

    /**
     * Получает данные о персонажах и сотрудниках для указанного аниме.
     * Выполняет HTTP-запрос для получения страницы аниме и парсит информацию о персонажах и сотрудниках.
     *
     * Fetches characters and staff data for a given anime.
     * Makes an HTTP request to retrieve the anime's page and parses character and staff information.
     */
    private function getAnimeCharactersAndStaffData(int $malId): JsonResponse
    {
        $url = config('malCrawler.base_url').config('malCrawler.anime_url').'/'.$malId.'/_/characters';

        $content = $this->handleHttpRequestErrors($this->httpClient, $url);

        if (! $content) {
            return response()->json([
                'malId' => $malId,
                'error' => 'Characters and staff Not Found',
            ]);
        }

        $crawler = new Crawler($content);

        $getCharacters = $this->getCharacters($crawler);

        return response()->json($getCharacters);
    }

    /**
     * Извлекает данные о персонажах аниме из DOM-структуры.
     * Проходит по HTML-странице аниме и собирает ссылки на персонажей.
     *
     * Extracts character data from the anime's DOM structure.
     * Crawls through the anime's HTML page and collects character links.
     */
    private function getCharacters(Crawler $crawler): array
    {
        return $crawler
            ->filterXPath('//div[contains(@class, "anime-character-container")]/table')
            ->each(
                function (Crawler $crawler) {
                    $name = $this->getCharacterName($crawler);
                    $favorites = $this->getCharacterFavorites($crawler);
                    $role = $this->getCharacterRole($crawler);
                    $characterUrl = $this->getCharacterUrl($crawler);
                    $image = $this->getCharacterImage($crawler);
                    $characterId = $this->getCharacterId($characterUrl);
                    $slug = $this->getSlug($name);
                    $voiceActors = $this->getVoiceActors($crawler);

                    return [
                        'id' => $characterId,
                        'slug' => $slug,
                        'name' => $name,
                        'favorites' => $favorites,
                        'role' => $role,
                        'image' => $image,
                        'url' => $characterUrl,
                        'voice_actors' => $voiceActors,
                    ];
                }
            );
    }

    /**
     * Извлекает ID персонажа из указанного URL.
     * Использует регулярное выражение для извлечения числового идентификатора.
     *
     * Retrieves the character ID from a given character URL.
     * Uses a regular expression to extract the numeric ID.
     */
    private function getCharacterId(string $characterUrl): ?int
    {
        preg_match('/\/character\/(\d+)\//', $characterUrl, $matches);

        return $matches[1] ?? null;
    }

    /**
     * Генерирует slug из имени персонажа.
     * Разбирает имя персонажа, чтобы извлечь путь, и преобразует его в slug.
     *
     * Generates a slug from a character's name.
     * Parses the character name to extract the path and transforms it into a slug.
     */
    private function getSlug(string $characterName): string
    {
        $slug = parse_url($characterName);

        return $this->generateSlug(basename($slug['path']));
    }

    /**
     * Извлекает URL изображения персонажа из предоставленной DOM-структуры.
     * Ищет в HTML структуре элемент изображения с определёнными атрибутами.
     *
     * Extracts the character's image URL from the given DOM structure.
     * Searches the HTML structure for an image element with specific attributes.
     */
    private function getCharacterImage(Crawler $crawler): string
    {
        return $crawler->filterXPath('//a[contains(@class, "fw-n")]/img')->attr('data-src');
    }

    /**
     * Извлекает URL персонажа из предоставленной DOM-структуры.
     * Находит ссылку на страницу персонажа в HTML-структуре.
     *
     * Extracts the character's URL from the given DOM structure.
     * Finds the link to the character's page in the HTML structure.
     */
    private function getCharacterUrl(Crawler $crawler): string
    {
        return $crawler->filterXPath('//a[contains(@class, "fw-n")]')->attr('href');
    }

    /**
     * Извлекает имя персонажа из предоставленной DOM-структуры.
     * Ищет элемент с именем персонажа в HTML-структуре.
     *
     * Extracts the character's name from the given DOM structure.
     * Searches for the element containing the character's name in the HTML structure.
     */
    private function getCharacterName(Crawler $crawler): string
    {
        return $crawler->filterXPath('//div[contains(@class, "spaceit_pad")]/a/h3[@class="h3_character_name"]')->text();
    }

    /**
     * Извлекает количество избранных у персонажа из предоставленной DOM-структуры.
     * Находит элемент с числом избранных в HTML-структуре.
     *
     * Extracts the number of favorites for the character from the given DOM structure.
     * Finds the element containing the favorites count in the HTML structure.
     */
    private function getCharacterFavorites(Crawler $crawler): string
    {
        return $crawler->filterXPath('//div[contains(@class, "js-anime-character-favorites")]')->text();
    }

    /**
     * Извлекает роль персонажа из предоставленной DOM-структуры.
     * Находит и возвращает роль, удаляя лишние пробелы.
     *
     * Extracts the character's role from the given DOM structure.
     * Finds and returns the role, trimming unnecessary whitespace.
     */
    private function getCharacterRole(Crawler $crawler): string
    {
        return trim($crawler->filterXPath('//div[contains(@class, "spaceit_pad") and not(a)]')->eq(0)->text());
    }

    /**
     * Извлекает список актёров озвучки из предоставленной DOM-структуры.
     * Парсит информацию о каждом актёре озвучки, включая имя, URL, язык, изображение и идентификатор.
     *
     * Extracts the list of voice actors from the given DOM structure.
     * Parses information about each voice actor, including name, URL, language, image, and ID.
     */
    private function getVoiceActors(Crawler $crawler): array
    {
        return $crawler
            ->filterXPath('//tr[contains(@class, "js-anime-character-va-lang")]')
            ->each(function (Crawler $actorsCrawler) {
                $name = $this->getVoiceActorName($actorsCrawler);
                $url = $this->getVoiceActorUrl($actorsCrawler);
                $slug = $this->getSlug($name);
                $id = $this->getVoiceActorId($url);
                $language = $this->getVoiceActorLanguage($actorsCrawler);
                $image = $this->getVoiceActorImage($actorsCrawler);

                return [
                    'id' => $id,
                    'slug' => $slug,
                    'name' => $name,
                    'language' => $language,
                    'image' => $image,
                    'url' => $url,
                ];
            });
    }

    /**
     * Извлекает имя актёра озвучки из предоставленной DOM-структуры.
     * Находит и возвращает имя актёра.
     *
     * Extracts the voice actor's name from the given DOM structure.
     * Finds and returns the actor's name.
     */
    private function getVoiceActorName(Crawler $crawler): ?string
    {
        return $crawler->filterXPath('//div[contains(@class, "spaceit_pad")]/a')->text() ?? null;
    }

    /**
     * Извлекает URL профиля актёра озвучки из предоставленной DOM-структуры.
     * Находит и возвращает ссылку на профиль актёра.
     *
     * Extracts the voice actor's profile URL from the given DOM structure.
     * Finds and returns the link to the actor's profile.
     */
    private function getVoiceActorUrl(Crawler $crawler): ?string
    {
        return $crawler->filterXPath('//div[contains(@class, "spaceit_pad")]/a')->attr('href') ?? null;
    }

    /**
     * Извлекает ID актёра озвучки из указанного URL.
     * Использует регулярное выражение для извлечения числового идентификатора из ссылки.
     *
     * Extracts the voice actor's ID from the given URL.
     * Uses a regular expression to extract the numeric identifier from the link.
     */
    private function getVoiceActorId(string $actorUrl): ?int
    {
        preg_match('/\/people\/(\d+)\//', $actorUrl, $matches);

        return $matches[1] ?? null;
    }

    /**
     * Извлекает язык актёра озвучки из предоставленной DOM-структуры.
     * Находит и возвращает язык, на котором говорит актёр.
     *
     * Extracts the voice actor's language from the given DOM structure.
     * Finds and returns the language spoken by the actor.
     */
    private function getVoiceActorLanguage(Crawler $crawler): ?string
    {
        return $crawler->filterXPath('//div[contains(@class, "spaceit_pad js-anime-character-language")]')->text() ?? null;
    }

    /**
     * Извлекает изображение актёра озвучки из предоставленной DOM-структуры.
     * Находит и возвращает URL изображения профиля актёра.
     *
     * Extracts the voice actor's image from the given DOM structure.
     * Finds and returns the URL of the actor's profile image.
     */
    private function getVoiceActorImage(Crawler $crawler): ?string
    {
        return $crawler->filterXPath('//div[contains(@class, "picSurround")]/a/img')->attr('data-src') ?? null;
    }
}
