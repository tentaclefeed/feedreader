> **Please note:** This package is still under development and not yet intended for productive usage but feel free to contribute.

# Tentaclefeed FeedReader

[![Packagist Version](https://img.shields.io/packagist/v/tentaclefeed/feedreader?style=flat-square)][packagist]
[![Packagist Downloads](https://img.shields.io/packagist/dt/tentaclefeed/feedreader?style=flat-square)][packagist]
[![Gitmoji](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat-square)](https://gitmoji.dev)
[![Packagist License](https://img.shields.io/packagist/l/tentaclefeed/feedreader?style=flat-square)][packagist]

With this package it is easy to discover, read and parse RSS and ATOM feeds.

## Installation

You can install this package via composer using:

```bash
composer require tentaclefeed/feedreader
```

The package will automatically register itself.

## Usage

### Discover Feeds

```php
use Tentaclefeed\Feedreader\Facades\FeedReader;

$feeds = FeedReader::discover('https://www.nytimes.com/');

/*
Illuminate\Support\Collection {#491
  #items: array:1 [
    0 => array:3 [
      "title" => "RSS",
      "type" => "application/rss+xml",
      "href" => "https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml"
    ]
  ]
}
*/
```

### Read Feed

```php
use Tentaclefeed\Feedreader\Facades\FeedReader;

$feed = FeedReader::read('https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml');

/* 
Tentaclefeed\Feedreader\Models\Feed {#430
  -title: "NYT > Top Stories"
  -subtitle: ""
  -updated_at: Carbon\Carbon @1619976397 {#485}
  -author: null
  -rights: "Copyright 2021 The New York Times Company"
  -items: Illumin…\Collection {#487}
}
*/
```

### API

#### `Tentaclefeed\Feedreader\Models\Feed`

| Method | Return type | Description |
|--------|-------------|-------------|
| `$feed->getTitle()` | `string` | The title of the feed. |
| `$feed->getSubtitle()` | `string` | The subtitle/description of the feed. |
| `$feed->getUpdatedAt()` | `Carbon\Carbon` | The last modification date of the feed. |
| `$feed->getAuthor()` | `Tentaclefeed\Feedreader\Models\Author` | The author of the feed. |
| `$feed->getRights()` | `string` | Copyright notices. |
| `$feed->getItems()` | `Illuminate\Support\Collection` | Articles from the feed. |

#### `Tentaclefeed\Feedreader\Models\Author`

| Method | Return type | Description |
|--------|-------------|-------------|
| `$author->getName()` | `string` | The name of the author. |
| `$author->getUri()` | `string` or `null` | The uri of the author. |

#### `Tentaclefeed\Feedreader\Models\FeedItem`

| Method | Return type | Description |
|--------|-------------|-------------|
| `$item->getId()` | `string` | The id of the item. |
| `$item->getTitle()` | `string` | The title of the item. |
| `$item->getCreatedAt()` | `Carbon\Carbon` | The datetime the item was published/created. |
| `$item->getUrl()` | `string` | The url of the item. |
| `$item->getSummary()` | `string` | The content summary of the item. |

## Testing

Run the tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[packagist]: https://packagist.org/packages/tentaclefeed/feedreader
