<?php


namespace Tentaclefeed\Feedreader\Exceptions;


use Exception;

class FeedNotFoundException extends Exception
{
    public $message = 'Feed could not be found.';
}
