# LaravelFacebookAdsSdk
This project is developing now.

```
    "require": {
        "facebook/php-ads-sdk": "^2.5"
    }
```


# Installation
```
$ composer require yish/laravel-facebook-php-ads-sdk
```

## app.php
```
//Provider
  Yish\LaravelFacebookAdsSdk\FacebookAdsServiceProvider::class,

//Facade
  'FacebookAds' => Yish\LaravelFacebookAdsSdk\Facades\LaravelFacebookAdsSdk::class,
```

# functions
Reference `FacebookAds\Object\Fields\AdAccountFields` consts.
```
  FacebookAds::getAdAccountList($facebookToken, ['ACCOUNT_ID', 'BUSINESS']);
```
or you can do on string for one.
```
  FacebookAds::getAdAccountList($facebookToken, 'ACCOUNT_ID');
```

Reference `FacebookAds\Object\Fields\CampaignFields` consts.
```
  FacebookAds::getCampaignList($facebookToken, $account_id, ['OBJECTIVE', 'NAME']);
```
or you can do on string for one.
```
  FacebookAds::getCampaignList($facebookToken, $account_id, 'OBJECTIVE');
```

continue...
