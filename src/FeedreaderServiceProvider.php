<?php

namespace Tentaclefeed\Feedreader;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FeedreaderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('tentaclefeedreader', function() {
            return new FeedReader();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
