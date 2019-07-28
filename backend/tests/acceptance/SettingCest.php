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

class SettingCest
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

    public function checkWebsite(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/setting/website'));
        $I->see('网站设置');
        $I->submitForm("button[type=submit]", [
            'SettingWebsiteForm[website_title]' => "testfeehicms",
        ]);
        $I->seeInField("SettingWebsiteForm[website_title]", "testfeehicms");
    }

    public function checkCustom(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/setting/custom'));
        $I->see('自定义设置');
        $I->submitForm("button[type=submit]", [
            'Options[20][value]' => "12345",
        ]);
        $I->seeInField("Options[20][value]", "12345");
    }
}