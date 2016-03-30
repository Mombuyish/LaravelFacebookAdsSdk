<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Api;

abstract class AbstractFacebookAdsSdk
{
    protected $config;

    /**
     * @var string
     */
    protected static $graphApiUrl = 'me';

    /**
     * Get the config settings and instance Api.
     *
     * @param $userFbToken
     * @return Api|null
     */
    protected function genFacebookApi($userFbToken)
    {
        $appId = $this->config['app_id'];
        $appSecret = $this->config['app_secret'];

        Api::init($appId, $appSecret, $userFbToken);

        return Api::instance();
    }
}