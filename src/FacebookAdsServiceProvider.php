<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Object\Fields\AdAccountFields;
use Illuminate\Support\ServiceProvider;

class FacebookAdsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/facebookAdsSdk.php' => \config_path('facebookAdsSdk.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Yish\LaravelFacebookAdsSdk\LaravelFacebookAdsSdk', function ($app) {
            $config = $app['config']->get('facebookAdsSdk.facebook_config');

            return new LaravelFacebookAdsSdk($config, new AdAccountFields);
        });
    }
}
