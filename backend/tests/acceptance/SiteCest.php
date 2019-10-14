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
        login($I);
    }

    public function checkMain(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/main'));
        $I->see("环境");
        $I->see("Web Server");
        $I->see("数据库信息");
    }

    public function checkLanguage(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/site/language', 'lang'=>'en-US']));
        //$I->setCookie("_csrf_backend", $I->grabCookie("_csrf_backend"));
        //$I->setCookie("BACKEND_FEEHICMS", $I->grabCookie("BACKEND_FEEHICMS"));
        $I->amOnPage(Url::toRoute(["site/main"]));
        $I->see("Web Server");
        $I->see("Database Info");
        $I->amOnPage(Url::toRoute(['/site/language', 'lang'=>'zh-CN']));
    }

    public function checkError(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/error'));
        $I->see("404");
    }
}