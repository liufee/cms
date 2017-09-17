<?php
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class RbacCest
{
    public $cookies = [];

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
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('登陆');
        $I->submitForm("button[name=login-button]", [
            'LoginForm[username]' => "admin",
            'LoginForm[password]' => 'password_0',
            'LoginForm[captcha]' => 'testme',
        ]);
        $I->seeCookie('_csrf_backend');
        $this->cookies = [
            '_' => $I->grabCookie("_csrf_backend"),
            'PHPSESSID' => $I->grabCookie("PHPSESSID")
        ];
    }

    private function setCookie(AcceptanceTester $I)
    {
        foreach ($this->cookies as $k => $v){
            $I->setHeader($k, $v);
        }
    }

    public function checkPermissions(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $I->see('路由');
        $I->see("描述");
    }

    public function checkRoles(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $I->see('角色');
        $I->see("描述");
    }

}