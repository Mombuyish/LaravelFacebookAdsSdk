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



    public function __construct($config, AdAccountFields $accountFields, CampaignFields $campaignFields, InsightsFields $insightsFields)
    {
        $this->config = $config;
        $this->accountFields = $accountFields;
        $this->campaignFields = $campaignFields;
        $this->insightsFields = $insightsFields;
    }

    /**
     * Transfer Ad account status $key => string.
     * @param $key
     * @return mixed
     * @throws LaravelFacebookAdsSdkException
     */
    public function transAdAccountStatus($key)
    {
        return $this->getAccountStatus($key);
    }

    /**
     * Transfer disable reason $key => string.
     * @param $key
     * @return mixed
     * @throws LaravelFacebookAdsSdkException
     */
    public function transDisableReason($key)
    {
        return $this->getDisableReason($key);
    }

    /**
     * @param $userFbToken
     * @param array $parameters
     * @return array
     */
    public function getAdAccountList($userFbToken, $parameters) : Array
    {
        $this->validate($userFbToken, $parameters);

        $accountsCursor = $this->getAdAccountsBySelf($userFbToken, $parameters);

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
        $this->validate($userFbToken, $account_id, $parameters);

        $fbApi = $this->init($userFbToken);

        $campaignsCursor = $this->getCampaignsByAdAccount($account_id, $parameters, $fbApi);

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
    public function getInsightList($userFbToken, $type, $ids, $parameters = ['IMPRESSIONS', 'SPEND'], $preset = 'last_30_days', $amount = 50) : Array
    {
        $this->validate($userFbToken, $ids, $parameters);

        if ( ! $this->inDatePreset($preset) || ! $this->inAdsType($type)) {
            throw new LaravelFacebookAdsSdkException(static::$exceptionMessage, 403);
        }

        $fbApi = $this->init($userFbToken);
        $fields = $this->getConstColumns((array)$parameters, 'Insights', false);

        $insightData = [];
        foreach (array_chunk((array)$ids, $amount) as $chunkIds) {

            foreach ($this->call("insights", "GET", [
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

    /**
     * @param $account_id
     * @param $parameters
     * @param $fbApi
     * @return \FacebookAds\Cursor
     * @throws LaravelFacebookAdsSdkException
     */
    private function getCampaignsByAdAccount($account_id, $parameters, Api $fbApi)
    {
        $acAccount = new AdAccount($this->prefix . $account_id, $fbApi);

        try {
            $campaignsCursor = $acAccount->getCampaigns($this->getConstColumns((array) $parameters, 'Campaign'));
        } catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }

        $campaignsCursor->setUseImplicitFetch(true);

        return $campaignsCursor;
    }

    /**
     * @param $userFbToken
     * @param $parameters
     * @return \FacebookAds\Cursor
     * @throws LaravelFacebookAdsSdkException
     */
    private function getAdAccountsBySelf($userFbToken, $parameters)
    {
        $user = new AdUser(static::$graphApiUrl, $this->init($userFbToken));

        try {
            $accountsCursor = $user->getAdAccounts($this->getConstColumns((array) $parameters, 'AdAccount'));
        } catch (RequestException $e) {
            throw new LaravelFacebookAdsSdkException($e->getMessage(), $e->getCode());
        }
        $accountsCursor->setUseImplicitFetch(true);

        return $accountsCursor;
    }
}