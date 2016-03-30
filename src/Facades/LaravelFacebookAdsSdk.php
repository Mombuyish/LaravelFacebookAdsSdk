<?php namespace Yish\LaravelFacebookAdsSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Yish\LaravelFacebookAdsSdk\LaravelFacebookAdsSdk
 */
class LaravelFacebookAdsSdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Yish\LaravelFacebookAdsSdk\LaravelFacebookAdsSdk::class;
    }
}
