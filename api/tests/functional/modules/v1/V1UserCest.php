<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:21
 */

namespace api\tests\functional;

use api\fixtures\UserFixture;
use api\tests\FunctionalTester;


class V1UserCest
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

    public function checkUsers(FunctionalTester $I)
    {
        $I->sendGET('/v1/users?access-token=' . $this->token);
        $I->canSeeResponseContains('feehi@feehi.com');
    }

    public function checkCreateUser(FunctionalTester $I)
    {
        $I->sendPOST('/v1/users?access-token=' . $this->token, [
            "username" => "feehi123",
            "password" => "123456",
            "email" => 'admin@feehi.com'
        ]);
        $I->canSeeResponseContains('admin@feehi.com');
    }

    public function checkUser(FunctionalTester $I)
    {
        $I->sendGET('/v1/users/1?access-token=' . $this->token);
        $I->canSeeResponseContains("feehi@feehi.com");
    }

    public function checkDeleteUser(FunctionalTester $I)
    {
        $I->sendDELETE("/v1/users/1?access-token=" . $this->token);
        $I->canSeeResponseCodeIs(204);
    }

    public function checkInfo(FunctionalTester $I)
    {
        $I->sendGET('/v1/user/info');
        $I->canSeeResponseContains('我是user无需token可以访问的info');
    }
}