<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:20
 */

namespace api\tests\functional;

use api\fixtures\UserFixture;
use api\tests\FunctionalTester;


class V1SiteCest
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

    public function checkLogin(FunctionalTester $I){

        $I->sendPOST("/v1/login", ["username"=>"feehi", "password"=>123456]);
        $I->canSeeResponseContains("accessToken");
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->sendGET('/v1');
        $I->canSeeResponseContains('feehi api service');
    }

    public function checkRegister(FunctionalTester $I)
    {

        $I->sendPOST("/v1/register", ["username"=>"a", "email"=>"afeehi.com", "password"=>123456]);
        $I->seeResponseContains('"success":false');

        $I->sendPOST("/v1/register", ["username"=>"aa", "email"=>"afeehi.com", "password"=>""]);
        $I->seeResponseContains('"success":false');

        $I->sendPOST("/v1/register", ["username"=>"aa", "email"=>"afeehi.com", "password"=>""]);
        $I->seeResponseContains('"success":false');

        $I->sendPOST("/v1/register", ["username"=>uniqid(), "email"=>"a@" . uniqid() . ".com", "password"=>123456]);
        $I->seeResponseContains('"success":true');
    }

}