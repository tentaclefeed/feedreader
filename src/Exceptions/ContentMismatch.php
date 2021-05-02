<?php

namespace Tentaclefeed\Feedreader\Exceptions;

use Exception;

class ContentMismatch extends Exception
{
    public $message = 'The given XML does not seem to be a valid feed.';
}
