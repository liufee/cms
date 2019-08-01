<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:10
 */

namespace api\tests\functional;


use api\fixtures\UserFixture;
use api\tests\FunctionalTester;

class UserCest
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
        $I->sendGET('/users?access-token=' . $this->token);
        $I->canSeeResponseContains('feehi@feehi.com');
    }

    public function checkCreateUser(FunctionalTester $I)
    {
        $I->sendPOST('/users?access-token=' . $this->token, [
            "username" => "feehi123",
            "password" => "123456",
            "email" => 'admin@feehi.com'
        ]);
        $I->canSeeResponseContains('admin@feehi.com');
    }

    public function checkUser(FunctionalTester $I)
    {
        $I->sendGET('/users/1?access-token=' . $this->token);
        $I->canSeeResponseContains("feehi@feehi.com");
    }

    public function checkDeleteUser(FunctionalTester $I)
    {
        $I->sendDELETE("/users/1?access-token=" . $this->token);
        $I->canSeeResponseCodeIs(204);
    }

    public function checkInfo(FunctionalTester $I)
    {
        $I->sendGET('/user/info');
        $I->canSeeResponseContains('我是user无需token可以访问的info');
    }
}