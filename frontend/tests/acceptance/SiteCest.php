<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class SiteCest
{
    public function checkSignup(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/signup'));
        $I->see('注册');
        $I->see('请填写下面信息完成注册');
        $I->fillField("#signupform-username", "feep");
        $I->fillField("#signupform-password", "111111");
        $I->fillField("#signupform-email", "test@feehi.com");
        $I->submitForm("button[name=signup-button]", []);
    }

    public function checkLogin(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('记住登录');

        $I->seeLink('重置');

        $I->fillField("#loginform-username", "feep");
        $I->fillField("#loginform-password", "111111");
        $I->submitForm("button[name=login-button]", []);
        $I->see("Welcome");
        $I->see("退出登录");
    }

    public function checkRequestPasswordReset(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/request-password-reset'));
        $I->see('重置密码');
    }

    public function checkResetPassword(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/reset-password'));
        $I->see("Bad Request");
    }

    public function checkLanguage(AcceptanceTester $I)
    {
        $I->setHeader("Referer", Url::toRoute('/article/index'));
        $I->amOnPage(Url::toRoute(['/site/language', 'lang'=>'en-US']));
        $I->see("About us");
    }

    public function checkOffline(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/offline'));
        $I->see("temporary unserviceable");
    }

    public function checkError(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/error'));
        $I->see("404");
    }
}
