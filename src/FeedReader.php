<?php

namespace Tentaclefeed\Feedreader;

use Illuminate\Support\Collection;

class FeedReader
{
    public function discover(string $url): bool|Collection
    {
        return (new Explorer())->discover($url);
    }
}
