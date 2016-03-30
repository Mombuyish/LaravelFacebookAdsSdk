<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LaravelFacebookAdsSdkTest extends TestCase
{
    protected $token;

    public function setUp()
    {
        parent::setUp();

        $this->token = env('ACCESS_TOKEN');
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
        $excepted = '';

        $result = FacebookAds::getAdAccountList($this->token, 'NAME');

        $this->assertEquals($excepted, $result[1]->name);
    }

    /**
     * @group fbadsdk
     * @test
     */
    public function 給陣列取得AdAccountList給予相對應欄位內容()
    {
        $excepted = ['balance' => 0, 'name' => ''];

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
        $account_id = '';

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
        $account_id = '';

        $excepted = ['objective' => 'LINK_CLICKS', 'name' => ''];

        $result = FacebookAds::getCampaignList($this->token, $account_id, ['OBJECTIVE', 'NAME']);

        $this->assertEquals($excepted, [
            'objective'  => $result[0]->objective,
            'name' => $result[0]->name,
        ]);
    }
}
