<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LaravelFacebookAdsSdkTest extends TestCase
{
    protected $token;
    protected $account_id;
    protected $username;
    protected $cpname;

    public function setUp()
    {
        parent::setUp();

        $this->token = env('ACCESS_TOKEN');

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
        $excepted = ["The params field is required."];

        $result = FacebookAds::getAdAccountList($this->token, '');

        $this->assertEquals($excepted, $result);
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
        $excepted = ['balance' => 0, 'name' => $this->username];

        $result = FacebookAds::getAdAccountList($this->token, ['BALANCE', 'NAME']);

        $this->assertEquals($excepted, [
            'balance'  => $result[1]->balance,
            'name' => $result[1]->name,
        ]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給Account_id取得CampaignList得到錯誤訊息()
    {
        $excepted = ['The params field or account_id field are required.'];

        $result = FacebookAds::getCampaignList($this->token, '', ['OBJECTIVE', 'ACCOUNT_ID']);

        $this->assertEquals($excepted, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 未給Parameters取得CampaignList得到錯誤訊息()
    {
        $excepted = ['The params field or account_id field are required.'];

        $result = FacebookAds::getCampaignList($this->token, 12345, '');

        $this->assertEquals($excepted, $result);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給字串取得CampaignList給予相對應欄位內容()
    {
        // if you want to test it, you must need account_id.
        $account_id = $this->account_id;

        $excepted = 'LINK_CLICKS';

        $result = FacebookAds::getCampaignList($this->token, $account_id,'OBJECTIVE');

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

        $excepted = ['objective' => 'LINK_CLICKS', 'name' => $this->cpname];

        $result = FacebookAds::getCampaignList($this->token, $account_id, ['OBJECTIVE', 'NAME']);

        $this->assertEquals($excepted, [
            'objective'  => $result[0]->objective,
            'name' => $result[0]->name,
        ]);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給錯誤的AdAccountStatus代碼會返回錯誤()
    {
        $excepted = 'This status does not exist';

        $result = FacebookAds::transAdAccountStatus(10000);

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
}
