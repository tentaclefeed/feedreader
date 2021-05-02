<?php

namespace Tentaclefeed\Feedreader\Exceptions;

use Exception;

class ContentTypeMismatch extends Exception
{
    public $message = 'The given Content-Type did non match "application/atom+xml" or "application/rss+xml".';
}
