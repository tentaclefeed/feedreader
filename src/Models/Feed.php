<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Tentaclefeed\Feedreader\Exceptions\ContentTypeMismatch;
use Tentaclefeed\Feedreader\Exceptions\FeedNotFoundException;
use Tentaclefeed\Feedreader\Exceptions\MissingContentTypeException;
use Tentaclefeed\Feedreader\Exceptions\ParseException;

class Feed
{
    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    /**
     * @return Author|null
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @return string|null
     */
    public function getRights(): ?string
    {
        return $this->rights;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    private string|null $title = null;

    private string|null $subtitle = null;

    private Carbon|null $updated_at = null;

    private Author|null $author = null;

    private string|null $rights = null;

    private Collection $items;

    /**
     * @param string $url
     *
     * @throws ContentTypeMismatch
     * @throws FeedNotFoundException
     * @throws MissingContentTypeException
     * @throws ParseException
     */
    public function __construct(string $url)
    {
        $this->init($url);
    }

    /**
     * @param string $url
     *
     * @return void
     *
     * @throws MissingContentTypeException
     * @throws ParseException
     * @throws ContentTypeMismatch
     * @throws FeedNotFoundException
     */
    protected function init(string $url): void
    {
        $response = $this->fetchUrl($url);

        $mimeType = $this->getMimeType($response);

        $xml = $this->parseXml($response->body());

        $this->parseFeed($xml, $mimeType);
    }

    /**
     * @param string $url
     *
     * @return Response
     *
     * @throws FeedNotFoundException
     */
    protected function fetchUrl(string $url): Response
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Tentaclefeed/1.0 FeedReader',
        ])->get($url);

        if (!$response->ok()) {
            throw new FeedNotFoundException();
        }

        return $response;
    }

    /**
     * @param Response $response
     *
     * @return string
     *
     * @throws ContentTypeMismatch
     * @throws MissingContentTypeException
     */
    protected function getMimeType(Response $response): string
    {
        $contentType = $response->header('Content-Type');

        if (!$contentType) {
            throw new MissingContentTypeException();
        }

        if (!Str::contains($contentType, 'application/atom+xml') &&
            !Str::contains($contentType, 'application/rss+xml')) {
            throw new ContentTypeMismatch();
        }

        preg_match('~application/(?:atom|rss)\+xml~', $contentType, $matches);

        return $matches[0];
    }

    /**
     * @param string $data
     *
     * @return SimpleXMLElement
     *
     * @throws ParseException
     */
    protected function parseXml(string $data): SimpleXMLElement
    {
        $xml = simplexml_load_string($data, null, LIBXML_NOCDATA | LIBXML_NOERROR);

        if (!$xml) {
            throw new ParseException();
        }

        return $xml;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param string           $mimeType
     *
     * @throws ContentTypeMismatch
     */
    protected function parseFeed(SimpleXMLElement $xml, string $mimeType): void
    {
        if ($mimeType === 'application/atom+xml') {
            $this->parseAtom($xml);
        } elseif ($mimeType === 'application/rss+xml') {
            $this->parseRss($xml);
        } else {
            throw new ContentTypeMismatch();
        }
    }

    protected function parseAtom(SimpleXMLElement $xml): void
    {
        $this->title = $xml->title;
        $this->subtitle = $xml->subtitle ?? null;
        $this->updated_at = $xml->updated ? Carbon::parse($xml->updated) : null;
        $this->author = $xml->author ? new Author($xml->author->name, $xml->author->uri) : null;
        $this->rights = $xml->rights ?? null;
        $this->items = new Collection();
        foreach ($xml->entry as $item) {
            $link = (string)collect($item->link->attributes())->get('href');
            $this->items->push(new FeedItem($item->id, $item->title, $item->published, $link, $item->summary));
        }
    }

    protected function parseRss(SimpleXMLElement $xml): void
    {
        $channel = $xml->channel;
        $this->title = $channel->title;
        $this->subtitle = $channel->description ?? null;
        $this->updated_at = Carbon::parse($channel->lastBuildDate);
        $this->rights = $channel->copyright ?? null;
        $this->items = new Collection();
        foreach ($channel->item as $item) {
            $this->items->push(
                new FeedItem($item->guid, $item->title, $item->pubDate, $item->link, $item->description),
            );
        }
    }
}
