<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Tentaclefeed\Feedreader\Exceptions\ContentMismatch;
use Tentaclefeed\Feedreader\Exceptions\FeedNotFoundException;
use Tentaclefeed\Feedreader\Exceptions\ParseException;

class Feed
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

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

    private string $url;

    private string|null $title = null;

    private string|null $subtitle = null;

    private Carbon|null $updated_at = null;

    private Author|null $author = null;

    private string|null $rights = null;

    private Collection $items;

    /**
     * @param string $url
     *
     * @throws ContentMismatch
     * @throws FeedNotFoundException
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
     * @throws ParseException
     * @throws ContentMismatch
     * @throws FeedNotFoundException
     */
    protected function init(string $url): void
    {
        $response = $this->fetchUrl($url);

        $this->url = $url;

        $xml = $this->parseXml($response->body());

        $this->parseFeed($xml);
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
     *
     * @return void
     *
     * @throws ContentMismatch
     */
    protected function parseFeed(SimpleXMLElement $xml): void
    {
        $name = strtolower($xml->getName());
        $namespace = strtolower(collect($xml->getNamespaces())->get(''));
        $attributes = collect($xml->attributes());
        if ($name === 'feed' && Str::endsWith($namespace, 'atom')) {
            $this->parseAtom($xml);
        } elseif (strtolower($name) === 'rss' && (string)$attributes->get('version') === '2.0') {
            $this->parseRss2($xml);
        } else {
            throw new ContentMismatch();
        }
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    protected function parseAtom(SimpleXMLElement $xml): void
    {
        $this->setTitle($xml->title);
        $this->setSubtitle($xml->subtitle ?? null);
        $this->setUpdatedAt($xml->updated);
        if ($xml->author) {
            $this->setAuthor($xml->author->name, $xml->author->uri);
        }
        $this->setRights($xml->rights ?? null);
        $this->items = new Collection();
        foreach ($xml->entry as $item) {
            $link = (string)collect($item->link->attributes())->get('href');
            $this->items->push(new FeedItem($item->id, $item->title, $item->published, $link, $item->summary));
        }
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    protected function parseRss2(SimpleXMLElement $xml): void
    {
        $channel = $xml->channel;
        $this->setTitle($channel->title);
        $this->setSubtitle($channel->description ?? null);
        $this->setUpdatedAt($channel->lastBuildDate);
        $this->setRights($channel->copyright ?? null);
        $this->items = new Collection();
        foreach ($channel->item as $item) {
            $this->items->push(
                new FeedItem($item->guid, $item->title, $item->pubDate, $item->link, $item->description),
            );
        }
    }

    /**
     * @param string|null $title
     */
    protected function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string|null $subtitle
     */
    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @param string|null $updated_at
     */
    protected function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at ? Carbon::parse($updated_at) : null;
    }

    /**
     * @param string      $name
     * @param string|null $uri
     */
    protected function setAuthor(string $name, ?string $uri): void
    {
        $this->author = new Author($name, $uri);
    }

    /**
     * @param string|null $rights
     */
    protected function setRights(?string $rights): void
    {
        $this->rights = $rights;
    }
}
