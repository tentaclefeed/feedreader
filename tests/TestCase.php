<?php

namespace Tentaclefeed\Feedreader\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tentaclefeed\Feedreader\FeedreaderServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            FeedreaderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
