<?php

namespace Tentaclefeed\Feedreader\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tentaclefeed\Feedreader\FeedreaderServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FeedreaderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // perform environment setup
    }
}
