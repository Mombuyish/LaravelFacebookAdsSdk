# LaravelFacebookAdsSdk
This project is developing now.

**update! facebook ads api 2.6 !**

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

Reference `FacebookAds\Object\Fields\InsightsFields` consts.

By default.
```
//DEFAULT:$parameters = ['IMPRESSIONS', 'SPEND'], $preset = 'last_30_days', $amount = 50
  FacebookAds::getInsightList($userFbToken, $type, $ids);
```
Or you can do this:
```
  FacebookAds::getInsightList($userFbToken, $type, $ids, ['COST_PER_UNIQUE_CLICK', 'SPEND'], $preset = 'lifetime', $amount = 50);
```
or you can do on string for one.
```
  FacebookAds::getInsightList($userFbToken, $type, $ids, 'COST_PER_UNIQUE_CLICK');
//$type = [
        'adaccount',
        'campaign',
        'adset',
        'ad',
];

//$preset @see https://developers.facebook.com/docs/marketing-api/reference/ad-campaign/insights/

```

# Exceptions
I add `LaravelFacebookAdsSdkException` to handle exceptions.
You can handle it in `App\Exceptions` handler.php
```
    if ( $e instanceof LaravelFacebookAdsSdkException ) {
        return respond($e->getMessage(), $e->getCode());
    }
```


# Transform
Transform AdAccount status you can call this:
```
    FacebookAds::transAdAccountStatus($adaccount_status);
```

//reference: https://developers.facebook.com/docs/marketing-api/reference/ad-account/#Reading
//account_status, Status of the account 
//1 = ACTIVE
//2 = DISABLED
//3 = UNSETTLED
//....

Transform DisableReason  status you can call this:
```
    FacebookAds::transDisableReason($disable_reason);
```
//reference: https://developers.facebook.com/docs/marketing-api/reference/ad-account/#Reading
//0 = NONE
//1 = ADS_INTEGRITY_POLICY
//2 = ADS_IP_REVIEW
//3 = RISK_PAYMENT
//4 = GRAY_ACCOUNT_SHUT_DOWN
//5 = ADS_AFC_REVIEW
