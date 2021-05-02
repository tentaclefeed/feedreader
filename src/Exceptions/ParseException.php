<?php

namespace Tentaclefeed\Feedreader\Exceptions;

use Exception;

class ParseException extends Exception
{
    public $message = 'An error occured while trying to parse the provided feed.';
}
