<?php

namespace Tentaclefeed\Feedreader\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Tentaclefeed\Feedreader\Models\Feed;

/**
 * @method static Collection discover(string $url)
 * @method static Feed read(string $url)
 *
 * @package Tentaclefeed\FeedReader\Facades
 */
class FeedReader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tentaclefeedreader';
    }
}
