<?php

namespace Tentaclefeed\Feedreader;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\UriInterface;

class Explorer
{
    protected string|null $iconUrl;
    protected UriInterface|null $effectiveUri = null;

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
        /** @var Response $response */
        $response = Http::withHeaders([
            'User-Agent' => 'Tentaclefeed/1.0 AutoDiscovery',
        ])->get($url);

        if ($response->transferStats) {
            $this->effectiveUri = $response->effectiveUri();
        }

        return $response->body();
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
                if ($this->effectiveUri) {
                    $href = UrlHelper::makeAbsoluteUrl($this->effectiveUri, $element->getAttribute('href'));
                } else {
                    $href = $element->getAttribute('href');
                }
                return [
                    'title' => $element->getAttribute('title'),
                    'icon' => $this->iconUrl,
                    'type' => $element->getAttribute('type'),
                    'href' => $href,
                ];
            });
        } catch (Exception) {
            return new Collection();
        }
    }
}
