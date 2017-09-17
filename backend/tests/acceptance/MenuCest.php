<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:37
 */
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class MenuCest
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

    public function checkIndex(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->amOnPage(Url::toRoute('/menu/index'));
        $I->see('后台菜单');
        $I->see("操作");
        $I->click("a[title=编辑]");
        $I->see("编辑后台菜单");
        $I->fillField("Menu[name]", '测试123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("Menu[name]", "测试123");
    }

}