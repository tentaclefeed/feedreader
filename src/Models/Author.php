<?php

namespace Tentaclefeed\Feedreader\Models;

class Author
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    private string $name;

    private string|null $uri;

    public function __construct(string $name, string $uri = null)
    {
        $this->name = $name;
        $this->uri = $uri;
    }
}
