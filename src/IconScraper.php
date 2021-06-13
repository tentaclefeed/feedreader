<?php

namespace Tentaclefeed\Feedreader;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Psr\Http\Message\UriInterface;

class IconScraper
{
    private string $url;
    private UriInterface $effectiveUri;

    public function __construct(string $url)
    {
        $this->url = $this->getHost($url);
    }

    public function scrape(): string|null
    {
        $icon = $this->scrapeUrl($this->url);

        if ($icon) {
            return $icon;
        }

        $host_names = explode(".", $this->url);
        $bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

        $domainIcon = $this->scrapeUrl($bottom_host_name);

        if ($domainIcon) {
            return $domainIcon;
        }

        return null;
    }

    protected function getHost(string $url): string
    {
        $host = parse_url($this->addProtocol($url), PHP_URL_HOST);

        if (!$host) {
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

    private function getIconUrl(Collection $collection): string
    {
        return $collection->sortBy(function (DOMElement $link) {
            $size = $link->getAttribute('sizes');

            if ($size) {
                preg_match('/(\d+)x(\d+)/', $size, $matches);
                if (count($matches) >= 2) {
                    return $matches[1];
                }
            }

            return $size;
        })->map(function (DOMElement $link) {
            $href = $link->getAttribute('href');
            if (!preg_match("~^https?://~i", $href)) {
                return UrlHelper::makeAbsoluteUrl($this->effectiveUri, $href);
            }
            return $href;
        })->first();
    }

    private function scrapeUrl($url): string|bool
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Tentaclefeed/1.0 IconScraper',
        ])->get($url);

        if (!$response || !$response->ok()) {
            return false;
        }

        $this->effectiveUri = $response->effectiveUri();

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

        try {
            $host = Http::withHeaders([
                'User-Agent' => 'Tentaclefeed/1.0 IconScraper',
            ])->get($url);

            $url = $host->effectiveUri()->getScheme() . '://' . $host->effectiveUri()->getHost();

            $favIcon = Http::withHeaders([
                'User-Agent' => 'Tentaclefeed/1.0 IconScraper',
            ])->get($url . '/favicon.ico');

            if ($favIcon->ok()) {
                Image::configure(['driver' => 'imagick']);
                $image = Image::make($favIcon->effectiveUri());
                return $image->encode('data-url');
            }
        } catch (Exception) {
            // Icon could not be scraped. Ignoring.
        }

        return false;
    }
}
