<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;

class FeedItem
{
    public string $id;

    public string $title;

    public Carbon $created_at;

    public string $url;

    public string $summary;

    public function __construct(
        string $id,
        string $title,
        string $created_at,
        string $url,
        string $summary,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->created_at = Carbon::parse($created_at);
        $this->url = $url;
        $this->summary = $summary;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function created_at(): Carbon
    {
        return $this->created_at;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function summary(): string
    {
        return $this->summary;
    }
}
