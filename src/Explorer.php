<?php

namespace Tentaclefeed\Feedreader;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Explorer
{
    protected string|null $iconUrl;

    public function discover(string $url): bool|Collection
    {
        $iconScraper = new IconScraper($url);
        $this->iconUrl = $iconScraper->scrape();

        $html = $this->fetchUrl($url);

        if ($html === false) {
            return false;
        }

        return $this->discoverFeedUrls($html);
    }

    private function fetchUrl(string $url): bool|string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Tentaclefeed/1.0 AutoDiscovery',
            ])->get($url);

            return $response->ok() ? $response->body() : false;
        } catch (Exception) {
            return false;
        }
    }

    private function discoverFeedUrls(string $html): Collection
    {
        try {
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            $xpath = new DOMXPath($dom);
            $query = '//*/link[@rel = "alternate" and @href and @type = "application/rss+xml" or @type = "application/atom+xml"]';
            $links = $xpath->query($query);

            return collect(iterator_to_array($links))->map(function (DOMElement $element) {
                return [
                    'title' => $element->getAttribute('title'),
                    'icon' => $this->iconUrl,
                    'type' => $element->getAttribute('type'),
                    'href' => $element->getAttribute('href'),
                ];
            });
        } catch (Exception) {
            return new Collection();
        }
    }
}
