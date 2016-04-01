<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\CampaignFields;

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

    public function __construct($config, AdAccountFields $accountFields, CampaignFields $campaignFields)
    {
        $this->config = $config;
        $this->accountFields = $accountFields;
        $this->campaignFields = $campaignFields;
    }

    public function transAdAccountStatus($key)
    {
        if ( ! array_key_exists($key, self::ADACCOUNT_STATUS)) return "This status does not exist";

        return self::ADACCOUNT_STATUS[$key];
    }

    protected function getConstColumns($consts = [], $type)
    {
        $result = [];

        foreach ($consts as $const) {
            $result[$const] = constant('\FacebookAds\Object\Fields\\' . $type . 'Fields::' . $const);
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
        if ( empty($parameters) ) {
            return array("The params field is required.");
        }

        $user = new AdUser(static::$graphApiUrl, $this->genFacebookApi($userFbToken));

        $accountsCursor = is_string($parameters) ? $user->getAdAccounts($this->getConstColumns(array($parameters),
            'AdAccount')) :
            $user->getAdAccounts($this->getConstColumns($parameters, 'AdAccount'));

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
        if ( empty($account_id) || empty($parameters) ) {
            return array("The params field or account_id field are required.");
        }

        $fbApi = $this->genFacebookApi($userFbToken);
        $acAccount = new AdAccount($this->prefix . $account_id, $fbApi);

        $campaignsCursor = is_string($parameters) ? $acAccount->getCampaigns($this->getConstColumns(array($parameters),
            'Campaign')) :
            $acAccount->getCampaigns($this->getConstColumns($parameters, 'Campaign'));

        $campaignsCursor->setUseImplicitFetch(true);

        $campaigns = [];
        while ($campaignsCursor->current()) {
            $campaigns[] = $campaignsCursor->current();

            $campaignsCursor->next();
        }

        return $campaigns;
    }
}