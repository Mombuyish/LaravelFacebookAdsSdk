<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Api;
use FacebookAds\Http\Exception\RequestException;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\InsightsFields;

class LaravelFacebookAdsSdk extends AbstractFacebookAdsSdk
{
    /**
     * @var
     */
    protected $config;
    /**
     * You can change prefix words when facebook update.
     * @var string
     */
    protected $prefix = 'act_';

    /**
     * @var AdAccountFields
     */
    private $accountFields;
    /**
     * @var CampaignFields
     */
    private $campaignFields;
    /**
     * @var InsightsFields
     */
    private $insightsFields;

    /**
     * @var array
     * see https://developers.facebook.com/docs/marketing-api/insights/v2.5
     * Parameters and Fields
     */
    const ADS_TYPE = [
        'adaccount',
        'campaign',
        'adset',
        'ad',
    ];

    /**
     * see https://developers.facebook.com/docs/marketing-api/reference/ad-account/#Reading
     */
    const ADACCOUNT_STATUS = [
        1   => 'ACTIVE',
        2   => 'DISABLED',
        3   => 'UNSETTLED',
        7   => 'PENDING_RISK_REVIEW',
        9   => 'IN_GRACE_PERIOD',
        100 => 'PENDING_CLOSURE',
        101 => 'CLOSED',
        102 => 'PENDING_SETTLEMENT',
        201 => 'ANY_ACTIVE',
        202 => 'ANY_CLOSED',
    ];

    /**
     * see https://developers.facebook.com/docs/marketing-api/reference/ad-campaign/insights/
     * date_preset
     */
    const PRESET = [
        'today',
        'yesterday',
        'last_3_days',
        'this_week',
        'last_week',
        'last_7_days',
        'last_14_days',
        'last_28_days',
        'last_30_days',
        'last_90_days',
        'this_month',
        'last_month',
        'this_quarter',
        'last_3_months',
        'lifetime',
    ];

    public function __construct($config, AdAccountFields $accountFields, CampaignFields $campaignFields, InsightsFields $insightsFields)
    {
        $this->config = $config;
        $this->accountFields = $accountFields;
        $this->campaignFields = $campaignFields;
        $this->insightsFields = $insightsFields;
    }

    public function transAdAccountStatus($key)
    {
        if ( !array_key_exists($key, self::ADACCOUNT_STATUS) ) {
            throw new LaravelFacebookAdsSdkException("This status does not exist", 403);
        }

        return self::ADACCOUNT_STATUS[$key];
    }

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
     * @param $userFbToken
     * @param array $parameters
     * @return array
     */
    public function getAdAccountList($userFbToken, $parameters) : Array
    {
        if ( empty($parameters) || empty($userFbToken)) {
            throw new LaravelFacebookAdsSdkException("The parameters field or user facebook token are required.", 403);
        }

        $user = new AdUser(static::$graphApiUrl, $this->genFacebookApi($userFbToken));

        try {
            $accountsCursor = $user->getAdAccounts($this->getConstColumns((array)$parameters, 'AdAccount'));
        }
        catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }
        $accountsCursor->setUseImplicitFetch(true);

        $adAccounts = [];
        while ($accountsCursor->current()) {
            $adAccounts[] = $accountsCursor->current();

            $accountsCursor->next();
        }

        return $adAccounts;
    }

    /**
     * @param $userFbToken
     * @param $account_id
     * @param array $parameters
     * @return array|string
     */
    public function getCampaignList($userFbToken, $account_id, $parameters) : Array
    {
        if ( empty($account_id) || empty($parameters) || empty($userFbToken)) {
            throw new LaravelFacebookAdsSdkException("The parameters field ,account_id field or user facebook token are required.", 403);
        }

        $fbApi = $this->genFacebookApi($userFbToken);
        $acAccount = new AdAccount($this->prefix . $account_id, $fbApi);

        try {
            $campaignsCursor = $acAccount->getCampaigns($this->getConstColumns((array)$parameters, 'Campaign'));
        }
        catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }
        $campaignsCursor->setUseImplicitFetch(true);

        $campaigns = [];
        while ($campaignsCursor->current()) {
            $campaigns[] = $campaignsCursor->current();

            $campaignsCursor->next();
        }

        return $campaigns;
    }

    /**
     * @param $userFbToken
     * @param $type
     * @param $ids
     * @param $parameters
     * @param string $preset
     * @param int $amount
     * @return array
     */
    public function getInsightList($userFbToken, $type, $ids, $parameters, $preset = 'lifetime', $amount = 50)
    {
        if ( empty($ids) || empty($parameters) || empty($type) || empty($userFbToken)) {
            throw new LaravelFacebookAdsSdkException("The params field are required.", 403);
        }

        if ( ! in_array($type, static::ADS_TYPE) ) {
            throw new LaravelFacebookAdsSdkException("Type does not in fields.", 403);
        }

        if ( ! in_array($preset, static::PRESET)) {
            throw new LaravelFacebookAdsSdkException("Preset does not in fields.", 403);
        }

        $fbApi = $this->genFacebookApi($userFbToken);

        $fields = $this->getConstColumns((array)$parameters, 'Insights', false);

        $insightData = [];
        foreach (array_chunk((array)$ids, $amount) as $chunkIds) {

            foreach ($this->customCall("insights", "GET", [
                "ids"    => $chunkIds,
                "preset" => $preset,
                "fields" => $fields,
            ],
                $fbApi) as $fbId => $insight) {
                $insightData[$fbId] = $insight;
            }
        }

        return $insightData;
    }

    protected function customCall($node, $method, $param, Api $fbApi)
    {
        try {
            $token = $fbApi->call("/" . $node, $method, $param)->getContent();
        }
        catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }

        return $token;
    }
}