<?php

namespace Meruhook\MeruhookSDK;

use Illuminate\Support\ServiceProvider;

class MeruServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/meru.php', 'meru');

        $this->app->singleton(MeruConnector::class, function () {
            return new MeruConnector(
                apiToken: config('meru.api_token'),
                baseUrl: config('meru.base_url')
            );
        });

        $this->app->alias(MeruConnector::class, 'meru');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/meru.php' => config_path('meru.php'),
            ], 'config');
        }
    }
}