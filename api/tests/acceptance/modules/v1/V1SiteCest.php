<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-07-28 21:58
 */
namespace api\tests;

use api\fixtures\UserFixture;
use api\tests\AcceptanceTester;

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

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function checkLogin(AcceptanceTester $I){

        $I->sendPOST("/v1/login", ["username"=>"feehi", "password"=>123456]);
        $I->canSeeResponseContains("accessToken");
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->sendGET('/v1');
        $I->canSeeResponseContains('feehi api service');
    }

    public function checkRegister(AcceptanceTester $I)
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