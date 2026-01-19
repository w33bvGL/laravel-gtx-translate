<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Providers;

use Illuminate\Support\ServiceProvider;
use W33bvgl\GtxTranslate\Commands\TranslateCommand;
use W33bvgl\GtxTranslate\Contracts\TranslatorInterface;
use W33bvgl\GtxTranslate\Services\GoogleGtxTranslator;

class GtxTranslateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = __DIR__.'/../../config/gtx-translate.php';

        $this->mergeConfigFrom($configPath, 'gtx-translate');

        $this->app->singleton(TranslatorInterface::class, function ($app) {
            return new GoogleGtxTranslator($app['config']->get('gtx-translate') ?? []);
        });

        $this->app->alias(TranslatorInterface::class, 'gtx-translate');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__.'/../../config/gtx-translate.php';

            $this->publishes([
                $configPath => config_path('gtx-translate.php'),
            ], 'gtx-translate-config');

            $this->commands([
                TranslateCommand::class,
            ]);
        }
    }
}
