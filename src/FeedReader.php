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

    public function read(string $url): Feed
    {
        return new Feed($url);
    }
}
