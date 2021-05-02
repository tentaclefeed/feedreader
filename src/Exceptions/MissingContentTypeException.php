<?php


namespace Tentaclefeed\Feedreader\Exceptions;


use Exception;

class MissingContentTypeException extends Exception
{
    public $message = 'The Content-Type header was missing from the response.';
}
