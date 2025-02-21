<?php

declare(strict_types=1);

namespace Anidzen\GoogleTranslateScraper\Providers;

use Anidzen\GoogleTranslateScraper\Services\Text\TextTranslateHideApiService;
use Illuminate\Support\ServiceProvider;

class GoogleTranslateScraperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('translate', function () {
            return [
                'text' => new TextTranslateHideApiService,
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
