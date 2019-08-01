<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-07-29 00:03
 */

namespace api\tests;

use api\fixtures\UserFixture;
use api\tests\AcceptanceTester;

class UserCest
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

    public function checkUsers(AcceptanceTester $I)
    {
        $I->sendGET('/users?access-token=' . $this->token);
        $I->canSeeResponseContains('feehi@feehi.com');
    }

    public function checkCreateUser(AcceptanceTester $I)
    {
        $I->sendPOST('/users?access-token=' . $this->token, [
                "username" => "feehi123",
                "password" => "123456",
                "email" => 'admin@feehi.com'
        ]);
        $I->canSeeResponseContains('admin@feehi.com');
    }

    public function checkUser(AcceptanceTester $I)
    {
        $I->sendGET('/users/1?access-token=' . $this->token);
        $I->canSeeResponseContains("feehi@feehi.com");
    }

    public function checkDeleteUser(AcceptanceTester $I)
    {
        $I->sendDELETE("/users/1?access-token=" . $this->token);
        $I->canSeeResponseCodeIs(204);
    }

   public function checkInfo(AcceptanceTester $I)
    {
        $I->sendGET('/user/info');
        $I->canSeeResponseContains('我是user无需token可以访问的info');
    }
}