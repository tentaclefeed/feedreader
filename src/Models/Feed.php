<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

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
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    /**
     * @param string|null $subtitle
     */
    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    /**
     * @param Carbon|null $updated_at
     */
    public function setUpdatedAt(?Carbon $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return Author|null
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author|null $author
     */
    public function setAuthor(?Author $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string|null
     */
    public function getRights(): ?string
    {
        return $this->rights;
    }

    /**
     * @param string|null $rights
     */
    public function setRights(?string $rights): void
    {
        $this->rights = $rights;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param Collection $items
     */
    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    private string|null $title = null;

    private string|null $subtitle = null;

    private Carbon|null $updated_at = null;

    private Author|null $author = null;

    private string|null $rights = null;

    private Collection $items;

    public function __construct(string $url)
    {
        $this->init($url);
    }

    protected function init(string $url): void
    {
        try {
            $response = Http::get($url);
            $xmlData = $response->body();
            $xml = simplexml_load_string($xmlData, null, LIBXML_NOCDATA | LIBXML_NOERROR);

            if (!$xml) {
                return;
            }

            $contentType = optional($response->headers()['Content-Type'])[0];

            if (!$contentType) {
                return;
            }

            if (preg_match("~application/atom\+xml~", $contentType)) {
                $this->parseAtom($xml);
            } elseif (preg_match("~application/rss\+xml~", $contentType)) {
                $this->parseRss($xml);
            }
        } catch (\Exception) {
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
