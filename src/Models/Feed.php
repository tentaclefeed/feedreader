<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class Feed
{
    public string|null $title = null;

    public string|null $subtitle = null;

    public Carbon|null $updated_at = null;

    public Author|null $author = null;

    public string|null $rights = null;

    public Collection $items;

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
        $this->items = new Collection();
        foreach ($channel->item as $item) {
            $this->items->push(
                new FeedItem($item->guid, $item->title, $item->pubDate, $item->link, $item->description),
            );
        }
    }
}
