<?php

namespace Yish\LaravelFacebookAdsSdk;

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdUser;
use FacebookAds\Object\Fields\AdAccountFields;

class LaravelFacebookAdsSdk extends AbstractFacebookAdsSdk
{
    /**
     * @var
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix = 'act_';

    /**
     * @var AdAccountFields
     */
    private $accountFields;

    public function __construct($config, AdAccountFields $accountFields)
    {
        $this->config = $config;
        $this->accountFields = $accountFields;
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