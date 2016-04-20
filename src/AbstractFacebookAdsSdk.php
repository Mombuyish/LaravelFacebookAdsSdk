<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Api;
use FacebookAds\Http\Exception\RequestException;

abstract class AbstractFacebookAdsSdk extends FacebookConstField
{
    protected $config;

    protected static $exceptionMessage = "Parameter field is required.";

    /**
     * @var string
     */
    protected static $graphApiUrl = 'me';

    /**
     * @var array
     * see https://developers.facebook.com/docs/marketing-api/insights/v2.6
     * Parameters and Fields
     */
    const ADS_TYPE = [
        'adaccount',
        'campaign',
        'adset',
        'ad',
    ];

    /**
     * Check ads type in allowed field.
     * @param $type
     * @return bool
     */
    public function inAdsType($type)
    {
        return in_array($type, static::ADS_TYPE);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isEmpty($value)
    {
        return empty($value);
    }

    public static function addSlash($name)
    {
        return "/" . $name;
    }

    public function validate()
    {
        foreach ($parameters = func_get_args() as $arg) {
            if ($this->isEmpty($arg)) {
                throw new LaravelFacebookAdsSdkException(static::$exceptionMessage, 403);
            }
        }

        return $parameters;
    }

    /**
     * Check date preset in allowed field.
     * @param $preset
     * @return bool
     */
    public function inDatePreset($preset)
    {
        return in_array($preset, static::date_preset);
    }


    /**
     * Get the config settings and instance Api.
     *
     * @param $userFbToken
     * @return Api|null
     */
    protected function init($userFbToken)
    {
        Api::init($this->config['app_id'], $this->config['app_secret'], $userFbToken);
        return Api::instance();
    }


    /**
     * @param $node
     * @param $method
     * @param $param
     * @param Api $fbApi
     * @return array
     * @throws LaravelFacebookAdsSdkException
     */
    protected function call($node, $method, $param, Api $fbApi)
    {
        try {
            $token = $fbApi->call(self::addSlash($node), $method, $param)->getContent();
        }
        catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }

        return $token;
    }

    /**
     * @param array $consts
     * @param $type
     * @param bool $needle
     * @return array
     */
    protected function getConstColumns($consts = [], $type, $needle = true)
    {
        $result = [];

        foreach ($consts as $const) {

            $data = constant('\FacebookAds\Object\Fields\\' . $type . 'Fields::' . $const);

            if ( $needle ) {
                $result[$const] = $data;
            } else {
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * Get Account Status from facebook.
     * @param $key
     * @return mixed
     * @throws LaravelFacebookAdsSdkException
     */
    public function getAccountStatus($key)
    {
        if ( ! array_key_exists($key, self::account_status) ) {
            throw new LaravelFacebookAdsSdkException("This status does not exist", 403);
        }

        return self::account_status[$key];
    }

    /**
     * @param $key
     * @return mixed
     * @throws LaravelFacebookAdsSdkException
     */
    public function getDisableReason($key)
    {
        if ( ! array_key_exists($key, self::disable_reason) ) {
            throw new LaravelFacebookAdsSdkException("This status does not exist", 403);
        }

        return self::disable_reason[$key];
    }
}