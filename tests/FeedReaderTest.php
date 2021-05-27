<?php

namespace Tentaclefeed\Feedreader\Tests;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tentaclefeed\Feedreader\Exceptions\FeedNotFoundException;
use Tentaclefeed\Feedreader\Exceptions\ParseException;
use Tentaclefeed\Feedreader\Facades\FeedReader;
use Tentaclefeed\Feedreader\Models\Author;
use Tentaclefeed\Feedreader\Models\Feed;

class FeedReaderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'nytimes.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/atom.xml'), Response::HTTP_OK, [
                'Content-Type' => 'application/atom+xml; charset=UTF-8',
            ]),
            'theguardian.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/rss.xml'), Response::HTTP_OK, [
                'Content-Type' => 'application/rss+xml; charset=UTF-8',
            ]),
            'example.com/*' => Http::response('Not found', Response::HTTP_NOT_FOUND),
            'github.com/*' => Http::response(file_get_contents(__DIR__ . '/mocks/atom.xml'), Response::HTTP_OK),
            'cnn.com/*' => Http::response('Wrong Content Type', Response::HTTP_OK, [
                'Content-Type' => 'text/html',
            ]),
            'google.com/*' => Http::response('No XML Code', Response::HTTP_OK, [
                'Content-Type' => 'application/atom+xml; charset=UTF-8',
            ]),
        ]);
    }

    /** @test */
    public function it_should_successfully_read_an_atom_feed(): void
    {
        $feed = FeedReader::read('https://nytimes.com/atom');

        self::assertInstanceOf(Feed::class, $feed);
        self::assertEquals('https://nytimes.com/atom', $feed->getUrl());
        self::assertEquals('Example ATOM Feed', $feed->getTitle());
        self::assertEquals('Example ATOM Feed Subtitle', $feed->getSubtitle());
        self::assertInstanceOf(Carbon::class, $feed->getUpdatedAt());
        self::assertInstanceOf(Author::class, $feed->getAuthor());
        self::assertEquals('John Doe', $feed->getAuthor()->getName());
        self::assertEquals('https://example.com', $feed->getAuthor()->getUri());
        self::assertEquals('Copyright (c) 2021 Example Company', $feed->getRights());
        self::assertCount(2, $feed->getItems());
    }

    /** @test */
    public function it_should_successfully_read_an_rss_feed(): void
    {
        $feed = FeedReader::read('https://theguardian.com/rss');

        self::assertInstanceOf(Feed::class, $feed);
        self::assertEquals('https://theguardian.com/rss', $feed->getUrl());
        self::assertEquals('Example RSS Feed', $feed->getTitle());
        self::assertEquals('Example RSS Feed Description', $feed->getSubtitle());
        self::assertInstanceOf(Carbon::class, $feed->getUpdatedAt());
        self::assertNull($feed->getAuthor());
        self::assertEquals('Copyright (c) 2021 Example Company', $feed->getRights());
        self::assertCount(2, $feed->getItems());
    }

    /** @test */
    public function it_should_throw_exception_if_url_returns_http_error(): void
    {
        $this->expectException(FeedNotFoundException::class);

        FeedReader::read('https://example.com/feed');
    }

    /** @test */
    public function it_should_throw_exception_if_url_returns_wrong_content_type(): void
    {
        $this->expectException(ParseException::class);

        FeedReader::read('https://cnn.com/feed');
    }

    /** @test */
    public function it_should_throw_exception_if_feed_could_not_be_parsed(): void
    {
        $this->expectException(ParseException::class);

        FeedReader::read('https://google.com/feed');
    }

    /** @test */
    public function it_should_cache_feed_requests(): void
    {
        $url = 'https://nytimes.com/atom';
        FeedReader::read('https://nytimes.com/atom');

        self::assertTrue(Cache::has('tf.fr.fr.' . sha1($url)));
    }
}
