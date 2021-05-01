<?php

namespace Tentaclefeed\Feedreader\Models;

class Author
{
    public string $name;

    public string|null $uri;

    public function __construct(string $name, string $uri = null)
    {
        $this->name = $name;
        $this->uri = $uri;
    }
}
