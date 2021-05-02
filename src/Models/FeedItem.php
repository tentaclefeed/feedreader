<?php

namespace Tentaclefeed\Feedreader\Models;

use Carbon\Carbon;

class FeedItem
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    private string $id;

    private string $title;

    private Carbon $created_at;

    private string $url;

    private string $summary;

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
}
