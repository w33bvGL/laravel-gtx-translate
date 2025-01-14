<?php

namespace AniMik\MalCrawler\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * Facade для аниме-краулера, который предоставляет доступ к различным методам
 * краулинга данных о аниме, таких как жанры, студии, рейтинги, сезоны, типы и другие.
 *
 * Этот фасад используется для взаимодействия с сервисом аниме, который может извлекать
 * данные из внешних источников, таких как MyAnimeList (MAL), и предоставлять эти данные
 * в удобном формате для дальнейшей обработки.

 * The facade methods are intended for both general use cases (e.g., retrieving a list of genres)
 * and more specific cases, such as fetching detailed anime data by its MAL ID. It also supports filtering
 * data based on various criteria, such as anime status, age ratings, and more.
 *
 * This facade works by providing a simplified interface to the underlying anime crawling logic,
 * ensuring that the implementation details are abstracted away from the rest of the application.
 * All data crawled via this facade is returned as JSON responses, making it easy to integrate into
 * any application that needs to process or display anime-related information.
 *
 * Example usage:
 * - `Translator::crawlGenres()`
 * - `Translator::crawlAnime(12345)`
 * - `Translator::crawlValidAnimeIds()`
 */
class Translator extends Facade
{
    /**
     * Получает фасад для доступа к сервису аниме.
     *
     * Returns the facade accessor for the anime service.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'translate';
    }

    /**
     * Краулит жанры аниме с MyAnimeList.
     * Этот метод извлекает все жанры аниме с MyAnimeList.
     * Результат возвращается в виде JSON-ответа.
     *
     * Crawls anime genres from MyAnimeList.
     * This method retrieves all anime genres from MyAnimeList.
     * The result is returned as a JSON response.
     *
     *  Example usage:
     *  - `Translator::crawlGenres()`
     */
    public static function crawlGenres(): JsonResponse
    {
        return app('anime')['genres']->crawlGenres();
    }

    /**
     * Краулит явные жанры аниме (например, для фильмов с откровенным содержанием).
     * Использует метод для краулинга жанров и фильтрует явные жанры.
     *
     * Crawls explicit anime genres (e.g., for adult content). It uses the genre crawling
     * method and applies filtering for explicit genres.
     *
     *  Example usage:
     *  - `Translator::crawlExplicitGenres()`
     */
    public static function crawlExplicitGenres(): JsonResponse
    {
        return app('anime')['genres']->crawlExplicitGenres();
    }

    /**
     * Краулит темы аниме (например, романтика, приключения).
     * Использует метод для краулинга жанров и фильтрует по темам.
     *
     * Crawls anime themes (e.g., romance, adventure). It uses the genre crawling method
     * and applies filtering for specific themes.
     *
     * Example usage:
     * - `Translator::crawlThemes()`
     */
    public static function crawlThemes(): JsonResponse
    {
        return app('anime')['genres']->crawlThemes();
    }

    /**
     * Краулит демографические группы аниме (например, шонен, сёнен, сэйнен и т.д.).
     * Использует метод для краулинга жанров и фильтрует по демографическим группам.
     *
     * Crawls anime demographics (e.g., shonen, shoujo, seinen, etc.). It uses the genre
     * crawling method and filters by demographics.
     *
     * Example usage:
     * - `Translator::crawlDemographics()`
     */
    public static function crawlDemographics(): JsonResponse
    {
        return app('anime')['genres']->crawlDemographics();
    }

    /**
     * Краулит описание жанра по его malId.
     * Используется для получения описания жанра, явного жанра или темы.
     *
     * Crawls genre description by its malId. This method allows retrieving a genre's
     * description, explicit genre description, or theme description.
     *
     * Example usage:
     * - `Translator::crawlGenreDescription($malId)`
     */
    public static function crawlGenreDescription(int $malId): JsonResponse
    {
        return app('anime')['genres']->crawlGenreDescription($malId);
    }

    /**
     * Краулит студии аниме.
     * Этот метод извлекает все студии аниме с MyAnimeList.
     * Результат возвращается в виде JSON-ответа.
     *
     * Crawls anime studios. This method retrieves all anime studios from MyAnimeList.
     * The result is returned as a JSON response.
     *
     * Example usage:
     * - `Translator::crawlStudios()`
     */
    public static function crawlStudios(): JsonResponse
    {
        return app('anime')['studios']->crawlStudios();
    }

    /**
     * Краулит информацию о студии по её malId.
     * Этот метод извлекает подробную информацию о студии аниме по её уникальному идентификатору.
     *
     * Crawls studio information by its malId. This method retrieves detailed information
     * about a studio by its unique identifier.
     *
     * Example usage:
     * - `Translator::crawlStudioInformation($malId)`
     */
    public static function crawlStudioInformation(int $malId): JsonResponse
    {
        return app('anime')['studios']->crawlStudioInformation($malId);
    }

    /**
     * Краулит рейтинги аниме.
     * Этот метод извлекает рейтинги аниме, которые могут быть использованы для определения
     * популярности или успеха аниме-сериалов и фильмов.
     *
     * Crawls anime rankings. Retrieves the rankings for anime, which may be used to identify
     * the popularity or performance of anime series or movies.
     *
     * Example usage:
     * - `Translator::crawlRankings()`
     */
    public static function crawlRankings(): JsonResponse
    {
        return app('anime')['rankings']->crawlRankings();
    }

    /**
     * Краулит сезоны аниме.
     * Этот метод извлекает все доступные сезоны аниме и их детали, такие как годы выпуска
     * и специфические характеристики.
     *
     * Crawls anime seasons. This method retrieves all available anime seasons and their
     * details, such as release years and specific characteristics.
     *
     * Example usage:
     * - `Translator::crawlSeasons()`
     */
    public static function crawlSeasons(): JsonResponse
    {
        return app('anime')['seasons']->crawlSeasons();
    }

    /**
     * Краулит типы аниме, такие как сериал, фильм, OVA, ONA и т.д.
     * Этот метод используется для фильтрации контента по типам аниме.
     *
     * Crawls anime types such as TV series, movies, OVA, ONA, etc. Useful for filtering content
     * by anime type.
     *
     * Example usage:
     * - `Translator::crawlTypes()`
     */
    public static function crawlTypes(): JsonResponse
    {
        return app('anime')['filters']->crawlTypes();
    }

    /**
     * Краулит статусы аниме, такие как завершённое, выходящее и т.д.
     * Этот метод используется для фильтрации аниме по статусу его выхода.
     *
     * Crawls anime statuses such as completed, airing, etc. This method helps in filtering
     * anime based on its release status.
     *
     * Example usage:
     * - `Translator::crawlStatus()`
     */
    public static function crawlStatus(): JsonResponse
    {
        return app('anime')['filters']->crawlStatus();
    }

    /**
     * Краулит возрастные рейтинги аниме, такие как G, PG, PG-13, R и т.д.
     * Этот метод используется для фильтрации контента по возрастным ограничениям.
     *
     * Crawls anime ratings such as G, PG, PG-13, R, etc. This method filters content by age
     * ratings to restrict or recommend certain anime based on the audience's age.
     *
     * Example usage:
     * - `Translator::crawlRated()`
     */
    public static function crawlRated(): JsonResponse
    {
        return app('anime')['filters']->crawlRated();
    }

    /**
     * Краулит столбцы аниме, отображаемые в результатах поиска или каталогах.
     * Это может включать название, рейтинг, студию, год выхода и другие данные.
     *
     * Crawls anime columns displayed in search results or catalogs. This may include information
     * like title, rating, studio, release year, and more.
     *
     * Example usage:
     * - `Translator::crawlColumns()`
     */
    public static function crawlColumns(): JsonResponse
    {
        return app('anime')['filters']->crawlColumns();
    }

    /**
     * Краулит информацию об аниме по его идентификатору на MyAnimeList (MAL).
     * Этот метод извлекает подробные данные о конкретном аниме.
     *
     * Crawls detailed information about a specific anime based on its MyAnimeList (MAL) ID.
     * This method retrieves comprehensive data about the anime.
     *
     * Example usage:
     * - `Translator::crawlAnime($malId)`
     */
    public static function crawlAnime(int $malId): JsonResponse
    {
        return app('anime')['anime']->crawlAnime($malId);
    }

    /**
     * Краулит информацию о персонажах и сотрудниках для всех аниме на основе их идентификатора MyAnimeList (MAL).
     * Этот метод извлекает подробные данные о персонажах и сотрудниках для указанного аниме.
     *
     * Crawls characters and staff information for a specific anime based on its MyAnimeList (MAL) ID.
     * This method retrieves detailed data about characters and staff for the given anime.
     *
     * Example usage:
     *  - `Translator::crawlAnimeCharactersAndStaff($malId)`
     */
    public static function crawlAnimeCharactersAndStaff(int $malId): JsonResponse
    {
        return app('anime')['charactersAndStaff']->crawlAnimeCharactersAndStaff($malId);
    }

    /**
     * Краулит данные о валидных ID аниме с внешнего ресурса или API.
     * Получает актуальный список ID, который затем можно использовать для различных целей, таких как фильтрация, массовая обработка
     * или получение подробной информации о каждом аниме.
     *
     * Crawls valid anime ID data from an external resource or API.
     * Fetches an up-to-date list of IDs, which can be used for various purposes such as filtering, bulk processing, or obtaining detailed information
     * about each anime.
     *
     * Example usage:
     * - `Translator::crawlValidAnimeIds()`
     */
    public static function crawlValidAnimeIds(): JsonResponse
    {
        return app('anime')['anime']->crawlValidAnimeIds();
    }

    /**
     * Этот метод принимает MAL ID (уникальный идентификатор аниме на MyAnimeList), отправляет HTTP-запрос на страницу списка эпизодов
     * и извлекает информацию о каждом эпизоде. Данные включают такие элементы, как номер эпизода и другая информация, доступная на странице.
     *
     * This method accepts the MAL ID (a unique identifier for an anime on MyAnimeList), sends an HTTP request to the episode list page,
     * and extracts information about each episode. The data includes elements such as the episode number and other available details from the page.
     *
     * Example usage:
     * - `Translator::crawlAnimeEpisodesList($malId)`
     */
    public static function crawlAnimeEpisodesList(int $malId): JsonResponse
    {
        return app('anime')['episodesList']->crawlAnimeEpisodesList($malId);
    }
}
