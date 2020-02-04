<?php

namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class UserCest
 */
class UserCest
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
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/user/index'));
        $I->see('用户名');
        $I->see("邮箱");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/user/create'));
        $I->fillField("User[username]", 'test_name');
        $I->fillField("User[password]", 'password');
        $I->fillField("User[repassword]", 'password');
        $I->fillField("User[email]", 'test@feehi.com');
        $I->submitForm("button[type=submit]", []);
        $I->see("test_name");
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/user/create'));
        $I->fillField("User[username]", 'test_name');
        $I->fillField("User[password]", 'password');
        $I->fillField("User[repassword]", 'password');
        $I->fillField("User[email]", 'test@feehi.com');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/user/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('user/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("success");
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/user/create'));
        $I->fillField("User[username]", 'test_name');
        $I->fillField("User[password]", 'password');
        $I->fillField("User[repassword]", 'password');
        $I->fillField("User[email]", 'test@feehi.com');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/user/index'));
        $I->click("a[title=编辑]");
        $I->fillField("User[email]", 'update@feehi.com');
        $I->seeInField("User[email]", "update@feehi.com");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/user/create'));
        $I->fillField("User[username]", 'test_name');
        $I->fillField("User[password]", 'password');
        $I->fillField("User[repassword]", 'password');
        $I->fillField("User[email]", 'test@feehi.com');
        $I->submitForm("button[type=submit]", []);

        $I->amOnPage(Url::toRoute('/user/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see("头像");
    }

}
