<?php

namespace AniMik\MalCrawler\Providers;

use AniMik\MalCrawler\Services\Anime\CrawlAnime;
use AniMik\MalCrawler\Services\Anime\CrawlAnimeEpisodes;
use AniMik\MalCrawler\Services\Anime\CrawlCharactersAndStaff;
use AniMik\MalCrawler\Services\Filter\CrawlAnimeFilters;
use AniMik\MalCrawler\Services\Genre\CrawlAnimeGenres;
use AniMik\MalCrawler\Services\Ranking\CrawlAnimeRankings;
use AniMik\MalCrawler\Services\Season\CrawlAnimeSeasons;
use AniMik\MalCrawler\Services\Studio\CrawlAnimeStudios;
use Illuminate\Support\ServiceProvider;

class MalCrawlerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Регистрирует сервис mal-crawler и его зависимости в контейнере сервисов.
     * Эти сервисы будут отвечать за парсинг жанров аниме, студий, рейтингов, сезонов и самих аниме.
     */
    public function register(): void
    {
        $this->app->singleton('anime', function () {
            return [
                'genres' => new CrawlAnimeGenres,
                'studios' => new CrawlAnimeStudios,
                'rankings' => new CrawlAnimeRankings,
                'seasons' => new CrawlAnimeSeasons,
                'filters' => new CrawlAnimeFilters,
                'anime' => new crawlAnime,
                'charactersAndStaff' => new CrawlCharactersAndStaff,
                'episodesList' => new CrawlAnimeEpisodes,
            ];
        });

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/mal-crawler.php', 'malCrawler'
        );
    }

    /**
     * Bootstrap any package services.
     *
     * Публикует конфигурационный файл mal-crawler для того, чтобы пользователь мог изменить настройки.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/mal-crawler.php' => config_path('mal-crawler.php'),
        ]);
    }
}
