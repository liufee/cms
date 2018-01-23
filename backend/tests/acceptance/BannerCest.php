<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-07 19:47
 */
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class BannerCest
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
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->see('Banner类型');
        $I->see("描述");
        $I->click("a[title=编辑]");
        $I->see("编辑Banner类型");
        $I->fillField("BannerTypeForm[tips]", 'banner类型描述');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("BannerTypeForm[tips]", "banner类型描述");
    }

    public function checkBanners(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $I->click("a[title=编辑]");
        $I->fillField("BannerForm[desc]", 'banner图片描述222');
        $I->submitForm("button[type=submit]", []);
        //$I->click("a[title=编辑]");
        $I->seeInField("BannerForm[desc]", "banner图片描述222");
    }

}