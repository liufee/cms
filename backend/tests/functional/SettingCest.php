<?php

namespace backend\tests\functional;

use common\models\AdminUser;
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
        $I->amLoggedInAs(AdminUser::findIdentity(1));
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

    public function checkSMTP(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/setting/smtp'));
        $I->see('SMTP设置');

        $I->submitForm("button[type=submit]", [
            'SettingSMTPForm[smtp_username]' => "test@feehi.com",
            'SettingSMTPForm[smtp_host]' => 'smtp.126.com',
        ]);
        $I->seeInField("SettingSMTPForm[smtp_username]", "test@feehi.com");
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
