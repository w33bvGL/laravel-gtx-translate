<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Providers;

use Anidzen\GoogleTranslateScraper\Services\Text\TextTranslateHideApi;
use Illuminate\Support\ServiceProvider;

class GoogleTranslateScraperServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton('translate', function () {
            return [
                'text' => new TextTranslateHideApi,
            ];
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/google-translate-scraper.php', 'googleTranslateScraper'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/google-translate-scraper.php' => config_path('google-translate-scraper.php'),
        ]);
    }
}
