<?php
/**
 * Here you can initialize variables via \Codeception\Util\Fixtures class
 * to store data in global array and use it in Cepts.
 *
 * ```php
 * // Here _bootstrap.php
 * \Codeception\Util\Fixtures::add('user1', ['name' => 'davert']);
 * ```
 *
 * In Cept
 *
 * ```php
 * \Codeception\Util\Fixtures::get('user1');
 * ```
 */
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

function login(AcceptanceTester $I){
    static $cookie = null;
    static $cookieBackend = null;
    if ($cookieBackend == null || $cookie == null) {
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('登录');
        $I->submitForm("button[name=login-button]", [
            'LoginForm[username]' => "admin",
            'LoginForm[password]' => 'password_0',
            'LoginForm[captcha]' => 'testme',
        ]);
        $I->seeCookie('_csrf_backend');
        $cookie = $I->grabCookie("_csrf_backend");
        $I->seeCookie('BACKEND_FEEHICMS');
        $cookieBackend = $I->grabCookie("BACKEND_FEEHICMS");
    }
    $I->setCookie("_csrf_backend", $cookie);
    $I->setCookie("BACKEND_FEEHICMS", $cookieBackend);
}