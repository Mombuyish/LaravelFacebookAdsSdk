<?php

namespace Yish\LaravelFacebookAdsSdk;

use Exception;

class LaravelFacebookAdsSdkException extends Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
         parent::__construct($message, $code, $previous);
    }
}