<?php

namespace Yish\LaravelFacebookAdsSdk;

use DateTime;
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


    public function __construct(
        $config,
        AdAccountFields $accountFields,
        CampaignFields $campaignFields,
        InsightsFields $insightsFields
    ) {
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
     * @param array $parameters
     * @param string $date_preset
     * @param null $time_range
     * @param int $amount
     * @return array
     * @throws LaravelFacebookAdsSdkException
     */
    public function getInsightList(
        $userFbToken,
        $type,
        $ids,
        $parameters = ['IMPRESSIONS', 'SPEND'],
        $date_preset = 'last_30_days',
        $time_range = null,
        $amount = 50
    ) : Array
    {
        $this->validate($userFbToken, $ids, $parameters);

        if ( (!empty($date_preset) && !$this->inDatePreset($date_preset)) || !$this->inAdsType($type) ) {
            throw new LaravelFacebookAdsSdkException(static::$exceptionMessage, 403);
        }

        $fbApi = $this->init($userFbToken);
        $fields = $this->getConstColumns((array) $parameters, 'Insights', false);

        $insightData = [];
        foreach (array_chunk((array) $ids, $amount) as $chunkIds) {

            $data = $this->setData($chunkIds, $fields, $date_preset, $time_range);

            foreach ($this->call("insights", "GET", $data,
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

    /**
     * date preset or time range choose of one.
     * @param $date_preset
     * @param $time_range
     * @param $chunkIds
     * @param $fields
     * @return array
     */
    protected function setData($chunkIds, $fields, $date_preset, $time_range)
    {
        $data = [
            "ids"    => $chunkIds,
            "fields" => $fields,
        ];

        //if time range does not empty, set time range and return.
        if ( !empty($time_range) && is_array($time_range) ) {
            if ( count($time_range) != 2 ) {
                throw new LaravelFacebookAdsSdkException('Time range format does not match.', 403);
            }
            $data = array_add($data, 'time_range.since', $time_range[0]);
            $data = array_add($data, 'time_range.until', $time_range[1]);

            return $data;
        }

        //low weight.
        //if date_preset does not empty, set date preset and return.
        if ( !empty($date_preset) ) {
            $data = array_add($data, 'date_preset', $date_preset);

            return $data;
        }

        return $data;
    }
}