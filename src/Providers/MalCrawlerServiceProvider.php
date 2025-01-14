<?php

namespace AniMik\MalCrawler\Providers;

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
        $this->app->singleton('translate', function () {
            return [
                'text' => new TranslateText,
            ];
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/mal-crawler.php', 'malCrawler'
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
            __DIR__.'/../../config/mal-crawler.php' => config_path('mal-crawler.php'),
        ]);
    }
}
