<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:19
 */

namespace api\tests\functional;


use api\fixtures\UserFixture;
use api\tests\FunctionalTester;

class V1PaidCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function _before(FunctionalTester $I)
    {
        $this->token = getTokenFunctional($I);
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->sendGET('/v1/paid/index');
        $I->canSeeResponseContains('"status":401');

        $I->sendGET('/v1/paid/index?access-token=' . $this->token);
        $I->canSeeResponseContains("我是v1 paid/index 需要access-token才能访问的接口");
    }

    public function checkInfo(FunctionalTester $I)
    {
        $I->sendGET('/v1/paid/info');
        $I->canSeeResponseContains('我是v1 paid/info 我不需要access-token也能访问');
    }
}