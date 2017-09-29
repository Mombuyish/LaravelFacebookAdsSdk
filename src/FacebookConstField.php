<?php

namespace Yish\LaravelFacebookAdsSdk;


abstract class FacebookConstField
{
    /**
     * see https://developers.facebook.com/docs/marketing-api/reference/ad-campaign-group/insights/
     * date_preset
     */
    const date_preset = [
        'today',
        'yesterday',
        'this_month',
        'last_month',
        'this_quarter',
        'lifetime',
        'last_3d',
        'last_7d',
        'last_14d',
        'last_28d',
        'last_30d',
        'last_90d',
        'last_week_mon_sun',
        'last_week_sun_sat',
        'last_quarter',
        'last_year',
        'this_week_mon_today',
        'this_week_sun_today',
        'this_year',
    ];

    /**
     * see https://developers.facebook.com/docs/marketing-api/reference/ad-account/#Reading
     */
    const account_status = [
        1 => 'ACTIVE',
        2 => 'DISABLED',
        3 => 'UNSETTLED',
        7 => 'PENDING_RISK_REVIEW',
        9 => 'IN_GRACE_PERIOD',
        100 => 'PENDING_CLOSURE',
        101 => 'CLOSED',
        102 => 'PENDING_SETTLEMENT',
        201 => 'ANY_ACTIVE',
        202 => 'ANY_CLOSED',
    ];

    /**
     * see https://developers.facebook.com/docs/marketing-api/reference/ad-account
     */
    const disable_reason = [
        0 => 'NONE',
        1 => 'ADS_INTEGRITY_POLICY',
        2 => 'ADS_IP_REVIEW',
        3 => 'RISK_PAYMENT',
        4 => 'GRAY_ACCOUNT_SHUT_DOWN',
        5 => 'ADS_AFC_REVIEW',
    ];
}