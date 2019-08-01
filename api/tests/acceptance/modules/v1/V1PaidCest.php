<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-07-28 23:40
 */

namespace api\tests\modules\v1;

use api\fixtures\UserFixture;
use api\tests\AcceptanceTester;

class V1PaidCest
{
    private $token;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    public function _before(AcceptanceTester $I)
    {
        $this->token = getToken($I);
    }

    public function _after(AcceptanceTester $I)
    {
    }


    public function checkIndex(AcceptanceTester $I)
    {
        $I->sendGET('/v1/paid/index');
        $I->canSeeResponseContains('"status":401');

        $I->sendGET('/v1/paid/index?access-token=' . $this->token);
        $I->canSeeResponseContains("我是v1 paid/index 需要access-token才能访问的接口");
    }

    public function checkInfo(AcceptanceTester $I)
    {
        $I->sendGET('/v1/paid/info');
        $I->canSeeResponseContains('我是v1 paid/info 我不需要access-token也能访问');
    }
}