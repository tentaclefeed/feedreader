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
```

### Read Feed

```php
use Tentaclefeed\Feedreader\Facades\FeedReader;

$feed = FeedReader::read('https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml');
```

[packagist]: https://packagist.org/packages/tentaclefeed/feedreader
