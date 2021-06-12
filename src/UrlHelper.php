<?php


namespace Tentaclefeed\Feedreader;


use Illuminate\Support\Str;
use Psr\Http\Message\UriInterface;

class UrlHelper
{
    public static function makeAbsoluteUrl(UriInterface $effectiveUri, string $path): string
    {
        if (Str::startsWith($path, '/')) {
            $scheme = $effectiveUri->getScheme();
            $host = $effectiveUri->getHost();
            $port = $effectiveUri->getPort();
            return $scheme . '://' . $host . ($port !== null ? ':' . $port : '') . $path;
        }

        if (!preg_match("~^https?://~i", $path)) {
            return $effectiveUri . $path;
        }

        return $path;
    }
}
