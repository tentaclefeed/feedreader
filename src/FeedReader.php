<?php

namespace Tentaclefeed\Feedreader;

use Illuminate\Support\Collection;
use Tentaclefeed\Feedreader\Models\Feed;

class FeedReader
{
    public function discover(string $url): bool|Collection
    {
        return (new Explorer())->discover($url);
    }

    /**
     * @throws Exceptions\ContentMismatch
     * @throws Exceptions\FeedNotFoundException
     * @throws Exceptions\ParseException
     */
    public function read(string $url): Feed
    {
        return new Feed($url);
    }
}
