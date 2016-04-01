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

## config publish
```
$ php artisan vendor:publish
```
.env
```
FB_APP_ID=
FB_APP_SECRET=
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

# Exceptions
if parameters is empty, you will got error *string*, I hope you handle this exceptions.
```
[
    'The params field is required.'
]
```

# Transform
Transform AdAccount  status you can call this:
```
//reference: https://developers.facebook.com/docs/marketing-api/reference/ad-account/#Reading
//account_status, Status of the account 
//1 = ACTIVE
//2 = DISABLED
//3 = UNSETTLED
//....
    FacebookAds::transAdAccountStatus($adaccount_status);
```


continue...
