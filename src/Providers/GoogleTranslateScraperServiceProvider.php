<?php

declare(strict_types=1);

namespace AniMik\MalCrawler\Providers;

use Illuminate\Support\ServiceProvider;

class GoogleTranslateScraperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Регистрирует сервис mal-crawler и его зависимости в контейнере сервисов.
     * Эти сервисы будут отвечать за парсинг жанров аниме, студий, рейтингов, сезонов и самих аниме.
     */
    public function register(): void
    {
        $this->app->singleton('translate', function () {
            return [
                'text' => new TranslateText,
            ];
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/google-translate-scraper.php', 'translateScraper'
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
            __DIR__.'/../../config/google-translate-scraper.php' => config_path('google-translate-scraper.php'),
        ]);
    }
}
