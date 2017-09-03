<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class SettingCest
 */
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

    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(User::findIdentity(1));
        $I->amOnRoute('setting/website');
    }

    public function checkWebsite(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/setting/website'));
        $I->see('网站设置');
        $I->submitForm("button[type=submit]", [
            'SettingWebsiteForm[website_title]' => "testfeehicms",
        ]);
        $I->seeInField("SettingWebsiteForm[website_title]", "testfeehicms");
    }

    public function checkCustom(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/setting/custom'));
        $I->see('自定义设置');
        $I->submitForm("button[type=submit]", [
            'Options[20][value]' => "12345",
        ]);
        $I->seeInField("Options[20][value]", "12345");
    }
}
