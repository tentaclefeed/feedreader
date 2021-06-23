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
     * @return string|null
     */
    public function getImageUrl(): string|null
    {
        return $this->imageUrl;
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
     * @return string|null
     */
    public function getSummary(): string|null
    {
        return $this->summary;
    }

    /*
     * @return string|null
     */
    public function getContent(): string|null
    {
        return $this->content;
    }

    private string $id;

    private string $title;

    private string|null $imageUrl;

    private Carbon $created_at;

    private string $url;

    private string|null $summary;

    private string|null $content;

    public function __construct(
        string $id,
        string $title,
        string $created_at,
        string $url,
        string|null $summary,
        string|null $content,
        string|null $imageUrl = null,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->created_at = Carbon::parse($created_at);
        $this->url = $url;
        $this->summary = $summary ?: null;
        $this->content = $content ?: null;
        $this->imageUrl = $imageUrl ?: null;
    }
}
