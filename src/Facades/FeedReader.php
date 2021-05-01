<?php

namespace Tentaclefeed\Feedreader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array discover(string $url)
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
