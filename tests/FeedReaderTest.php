<?php

namespace Tentaclefeed\Feedreader\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tentaclefeed\Feedreader\Facades\FeedReader;
use Tentaclefeed\Feedreader\Models\Author;
use Tentaclefeed\Feedreader\Models\Feed;

class FeedReaderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'nytimes.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/atom.xml'), 200, [
                'Content-Type' => 'application/atom+xml; charset=UTF-8',
            ]),
            'theguardian.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/rss.xml'), 200, [
                'Content-Type' => 'application/rss+xml; charset=UTF-8',
            ]),
        ]);
    }

    /** @test */
    public function it_should_successfully_read_an_atom_feed()
    {
        $feed = FeedReader::read('https://nytimes.com/atom');

        self::assertInstanceOf(Feed::class, $feed);
        self::assertEquals('Example ATOM Feed', $feed->getTitle());
        self::assertEquals('Example ATOM Feed Subtitle', $feed->getSubtitle());
        self::assertInstanceOf(Carbon::class, $feed->getUpdatedAt());
        self::assertInstanceOf(Author::class, $feed->getAuthor());
        self::assertEquals('John Doe', $feed->getAuthor()->name);
        self::assertEquals('https://example.com', $feed->getAuthor()->uri);
        self::assertEquals('Copyright (c) 2021 Example Company', $feed->getRights());
        self::assertCount(2, $feed->getItems());
    }

    /** @test */
    public function it_should_successfully_read_an_rss_feed()
    {
        $feed = FeedReader::read('https://theguardian.com/rss');

        self::assertInstanceOf(Feed::class, $feed);
        self::assertEquals('Example RSS Feed', $feed->getTitle());
        self::assertEquals('Example RSS Feed Description', $feed->getSubtitle());
        self::assertInstanceOf(Carbon::class, $feed->getUpdatedAt());
        self::assertNull($feed->getAuthor());
        self::assertEquals('Copyright (c) 2021 Example Company', $feed->getRights());
        self::assertCount(2, $feed->getItems());
    }
}
