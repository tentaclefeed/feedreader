<?php

namespace Tentaclefeed\Feedreader;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class IconScraper
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $this->getHost($url);
    }

    public function scrape(): string|null
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Tentaclefeed/1.0 IconScraper',
        ])->get($this->url);

        if (!$response || !$response->ok()) {
            return false;
        }

        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new DOMXPath($dom);

        $touchIconQuery = '//*/link[@rel = "apple-touch-icon" or @rel = "apple-touch-icon-precomposed"]';
        $appleTouchIcons = collect(iterator_to_array($xpath->query($touchIconQuery)));

        if ($appleTouchIcons->count()) {
            return $this->getIconUrl($appleTouchIcons);
        }

        $pngIconQuery = '//*/link[@rel = "icon" and @type = "image/png"]';
        $pngIcons = collect(iterator_to_array($xpath->query($pngIconQuery)));

        if ($pngIcons->count()) {
            return $this->getIconUrl($pngIcons);
        }

        return null;
    }

    protected function getHost(string $url): string
    {
        if (!($host = parse_url($this->addProtocol($url), PHP_URL_HOST))) {
            return $url;
        }

        return $host;
    }

    private function addProtocol(string $url): string
    {
        if (!preg_match("~^https?://~i", $url)) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    private function makeAbsoluteUrl(string $path): string
    {
        if (!preg_match("~^https?://~i", $path)) {
            return $this->addProtocol($this->url) . $path;
        }

        return $this->addProtocol($path);
    }

    private function getIconUrl(Collection $collection): string
    {
        return $collection->sortBy(function (DOMElement $link) {
            $size = $link->getAttribute('sizes');

            if ($size) {
                preg_match('/(\d+)x(\d+)/', $size, $matches);
                return $matches[1];
            }

            return $size;
        })->map(function (DOMElement $link) {
            return $this->makeAbsoluteUrl($link->getAttribute('href'));
        })->first();
    }
}
