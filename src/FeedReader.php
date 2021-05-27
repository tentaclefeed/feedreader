<?php

namespace Tentaclefeed\Feedreader;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tentaclefeed\Feedreader\Models\Feed;

class FeedReader
{
    public function discover(string $url): bool|Collection
    {
        $key = 'tf.fr.ex.' . sha1($url);
        $ttl = config('feedreader.cache.explorer.seconds', 86400);

        return Cache::remember($key, $ttl, function () use ($url) {
            return (new Explorer())->discover($url);
        });
    }

    public function read(string $url): Feed
    {
        $key = 'tf.fr.fr.' . sha1($url);
        $ttl = config('feedreader.cache.reader.seconds', 1800);

        return Cache::remember($key, $ttl, function () use ($url) {
            return new Feed($url);
        });
    }
}
