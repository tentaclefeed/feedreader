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
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @param Carbon $created_at
     */
    public function setCreatedAt(Carbon $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
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
