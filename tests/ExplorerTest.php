<?php

namespace Tentaclefeed\Feedreader\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Tentaclefeed\Feedreader\Facades\FeedReader;

class ExplorerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'nytimes.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/website-with-feeds.html')),
            'theguardian.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/website-with-atom-feed.html')),
            'cnn.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/website-with-rss-feed.html')),
            'github.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/website-without-feeds.html')),
        ]);
    }

    /** @test */
    public function it_should_find_atom_and_rss_feeds(): void
    {
        $feeds = FeedReader::discover('https://nytimes.com/');

        self::assertInstanceOf(Collection::class, $feeds);
        self::assertCount(2, $feeds);
        self::assertContains([
            'title' => 'ATOM Feed',
            'type' => 'application/atom+xml',
            'href' => 'https://example.com/atom',
        ], $feeds);
        self::assertContains([
            'title' => 'RSS Feed',
            'type' => 'application/rss+xml',
            'href' => 'https://example.com/rss',
        ], $feeds);
    }

    /** @test */
    public function it_should_find_only_atom_feed(): void
    {
        $feeds = FeedReader::discover('https://theguardian.com/');

        self::assertInstanceOf(Collection::class, $feeds);
        self::assertCount(1, $feeds);
        self::assertContains([
            'title' => 'ATOM Feed',
            'type' => 'application/atom+xml',
            'href' => 'https://example.com/atom',
        ], $feeds);
        self::assertNotContains([
            'title' => 'RSS Feed',
            'type' => 'application/rss+xml',
            'href' => 'https://example.com/rss',
        ], $feeds);
    }

    /** @test */
    public function it_should_find_only_rss_feed(): void
    {
        $feeds = FeedReader::discover('https://cnn.com/');

        self::assertInstanceOf(Collection::class, $feeds);
        self::assertCount(1, $feeds);
        self::assertNotContains([
            'title' => 'ATOM Feed',
            'type' => 'application/atom+xml',
            'href' => 'https://example.com/atom',
        ], $feeds);
        self::assertContains([
            'title' => 'RSS Feed',
            'type' => 'application/rss+xml',
            'href' => 'https://example.com/rss',
        ], $feeds);
    }

    /** @test */
    public function it_should_return_empty_collection_when_there_are_no_feeds(): void
    {
        $feeds = FeedReader::discover('https://github.com/');

        self::assertInstanceOf(Collection::class, $feeds);
        self::assertEmpty($feeds);
    }
}
