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

class SiteCest
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

    public function checkLogin(AcceptanceTester $I)
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

    public function checkMain(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->amOnPage(Url::toRoute('/site/main'));
        $I->see("环境");
        $I->see("Web Server");
        $I->see("数据库信息");
    }

    public function checkLanguage(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->setHeader("Referer", '/admin' . Url::toRoute('/site/main'));
        $I->amOnPage(Url::toRoute(['/site/language', 'lang'=>'en-US']));
        $I->see("Web Server");
        $I->see("Database Info");
    }

    public function checkError(AcceptanceTester $I)
    {
        $this->setCookie($I);
        $I->amOnPage(Url::toRoute('/site/error'));
        $I->see("404");
    }

    private function setCookie(AcceptanceTester $I)
    {
        foreach ($this->cookies as $k => $v){
            $I->setCookie($k, $v);
        }
    }
}