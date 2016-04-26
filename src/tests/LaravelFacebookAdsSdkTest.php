<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Yish\LaravelFacebookAdsSdk\LaravelFacebookAdsSdkException;

class LaravelFacebookAdsSdkTest extends TestCase
{
    protected $token;
    protected $account_id;
    protected $username;
    protected $cpname;

    public function setUp()
    {
        parent::setUp();

        $this->token = env('YISH_TOKEN');

        $this->account_id = env('ACCOUNT_ID');

        $this->username = env('USERNAME');

        $this->cpname = env('CPNAME');
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給參數取得AdAccountList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getAdAccountList($this->token, '');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給token參數取得AdAccountList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getAdAccountList('', 'NAME');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給錯誤token參數取得AdAccountList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getAdAccountList(123456, 'NAME');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給字串取得AdAccountList給予相對應欄位內容()
    {
        $excepted = $this->username;

        $result = FacebookAds::getAdAccountList($this->token, 'NAME');

        $this->assertEquals($excepted, $result[1]->name);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給陣列取得AdAccountList給予相對應欄位內容()
    {
        $excepted = ['balance' => 29448, 'name' => $this->username];

        $result = FacebookAds::getAdAccountList($this->token, ['BALANCE', 'NAME']);

        $this->assertEquals($excepted, [
            'balance' => $result[1]->balance,
            'name'    => $result[1]->name,
        ]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給Account_id取得CampaignList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getCampaignList($this->token, '', ['OBJECTIVE', 'ACCOUNT_ID']);
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給fb_token取得CampaignList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getCampaignList('', 12345, ['OBJECTIVE', 'ACCOUNT_ID']);
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給錯誤fb_token取得CampaignList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getCampaignList(33333, 12345, ['OBJECTIVE', 'ACCOUNT_ID']);
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給Parameters取得CampaignList得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getCampaignList($this->token, 12345, '');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給字串取得CampaignList給予相對應欄位內容()
    {
        // if you want to test it, you must need account_id.
        $account_id = $this->account_id;

        $excepted = 'MOBILE_APP_INSTALLS';

        $result = FacebookAds::getCampaignList($this->token, $account_id, 'OBJECTIVE');

        $this->assertEquals($excepted, $result[0]->objective);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給陣列取得CampaignList給予相對應欄位內容()
    {
        // if you want to test it, you must need account_id.
        $account_id = $this->account_id;

        $excepted = ['objective' => 'MOBILE_APP_INSTALLS', 'name' => $this->cpname];

        $result = FacebookAds::getCampaignList($this->token, $account_id, ['OBJECTIVE', 'NAME']);

        $this->assertEquals($excepted, [
            'objective' => $result[0]->objective,
            'name'      => $result[0]->name,
        ]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給錯誤的AdAccountStatus代碼會返回錯誤()
    {
        $result = '';
        try {
            FacebookAds::transAdAccountStatus(10000);
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給錯誤的DisableReason代碼會返回錯誤()
    {
        $result = '';
        try {
            FacebookAds::transDisableReason(10000);
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給4會給對應的DisableReason字串()
    {
        $excepted = 'GRAY_ACCOUNT_SHUT_DOWN';

        $result = FacebookAds::transDisableReason(4);

        $this->assertEquals($excepted, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給7會給對應的AdAccountStatus字串()
    {
        $excepted = 'PENDING_RISK_REVIEW';

        $result = FacebookAds::transAdAccountStatus(7);

        $this->assertEquals($excepted, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給全部參數取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, '', '', '');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給ACCOUNT_ID參數取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, 'adaccount', '', 'COST_PER_UNIQUE_CLICK');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給PARAMETER參數取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, 'adaccount', env('ACCOUNT_ID'), '');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給AD_TYPE參數取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, '', env('ACCOUNT_ID'), 'COST_PER_UNIQUE_CLICK');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給予的AD_TYPE不在TYPE內取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, 'adaccount1', env('ACCOUNT_ID'), 'COST_PER_UNIQUE_CLICK');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給予的PRESET不在TYPE內取得Insights得到錯誤訊息()
    {
        $result = '';
        try {
            FacebookAds::getInsightList($this->token, 'adaccount', env('ACCOUNT_ID'), 'COST_PER_UNIQUE_CLICK',
                '123456');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給字串取得InsightList給予相對應欄位內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
            'COST_PER_UNIQUE_CLICK', 'lifetime');

        $this->assertArrayHasKey('cost_per_unique_click', $result[env('ADOBJECT_ID_1')]['data'][0]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給字串和last_7_days區間取得InsightList給予相對應欄位內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
            'COST_PER_UNIQUE_CLICK', 'last_7_days');

        $this->assertArrayHasKey('cost_per_unique_click', $result[env('ADOBJECT_ID_1')]['data'][0]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給陣列取得InsightList給予相對應欄位內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
            ['COST_PER_UNIQUE_CLICK', 'SPEND'], 'lifetime');

        $this->assertArrayHasKey('cost_per_unique_click', $result[env('ADOBJECT_ID_1')]['data'][0]);
        $this->assertArrayHasKey('spend', $result[env('ADOBJECT_ID_1')]['data'][0]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 取得InsightList給予相對應欄位內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
            ['COST_PER_UNIQUE_CLICK', 'SPEND'], 'lifetime');

        $this->assertArrayHasKey(env('ADOBJECT_ID_1'), $result);
        $this->assertArrayHasKey(env('ADOBJECT_ID_2'), $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給予空白的fb_token驗證失敗()
    {
        $result = '';
        try {
            FacebookAds::getInsightList('', 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
                ['COST_PER_UNIQUE_CLICK', 'SPEND'], 'lifetime');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給予的fb_token驗證失敗()
    {
        $result = '';

        try {
            FacebookAds::getInsightList(1234556, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')],
                ['COST_PER_UNIQUE_CLICK', 'SPEND'], 'lifetime');
        } catch (LaravelFacebookAdsSdkException $e) {
            $result = $e;
        }

        $this->assertInstanceOf(LaravelFacebookAdsSdkException::class, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 取得InsightList不給予欄位取得default內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')]);

        $this->assertArrayHasKey(env('ADOBJECT_ID_1'), $result);
        $this->assertArrayHasKey(env('ADOBJECT_ID_2'), $result);
    }

    /**
     * @group fbadsdk1
     * @test
     */
    public function 給date_preset的last_7_days取得InsightList內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')], ['DATE_START', 'DATE_STOP'], 'last_7_days');

        $this->assertArrayHasKey('date_start', $result[env('ADOBJECT_ID_1')]['data'][0]);
        $this->assertArrayHasKey('date_stop', $result[env('ADOBJECT_ID_1')]['data'][0]);
        $this->assertEquals((new DateTime())->format('Y-m-d'), $result[env('ADOBJECT_ID_1')]['data'][0]['date_stop']);
        $this->assertEquals((new DateTime())->modify('-6 day')->format('Y-m-d'), $result[env('ADOBJECT_ID_1')]['data'][0]['date_start']);
    }

    /**
     * @group fbadsdk1
     * @test
     */
    public function 給date_preset的this_month取得InsightList內容()
    {
        $result = FacebookAds::getInsightList($this->token, 'adaccount', [env('ADOBJECT_ID_1'), env('ADOBJECT_ID_2')], ['DATE_START', 'DATE_STOP'], 'this_month');

        $this->assertArrayHasKey('date_start', $result[env('ADOBJECT_ID_1')]['data'][0]);
        $this->assertArrayHasKey('date_stop', $result[env('ADOBJECT_ID_1')]['data'][0]);
        $this->assertEquals((new DateTime())->modify('first day of this month')->format('Y-m-d'), $result[env('ADOBJECT_ID_1')]['data'][0]['date_start']);
    }
}
