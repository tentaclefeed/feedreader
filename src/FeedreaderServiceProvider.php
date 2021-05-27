<?php

namespace Tentaclefeed\Feedreader;

use Illuminate\Support\ServiceProvider;

class FeedreaderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('tentaclefeedreader', function () {
            return new FeedReader();
        });
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'feedreader');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('feedreader.php'),
            ], 'config');
        }
    }
}
