<?php

namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class AdminUserCest
 */
class AdminUserCest
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
        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $I->see('管理员');
        $I->see("用户名");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/create'));
        $I->fillField("AdminUser[username]", 'test_name');
        $I->fillField("AdminUser[password]", 'password');
        $I->fillField("AdminUser[email]", 'test@feehi.com');
        $I->fillField("AdminUser[roles]", "");
        $I->submitForm("button[type=submit]", ["AdminUser[permissions][sdfsf:POST]"=>0]);
        $I->see("test_name");
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/create'));
        $I->fillField("AdminUser[username]", 'test_name');
        $I->fillField("AdminUser[password]", 'password');
        $I->fillField("AdminUser[email]", 'test@feehi.com');
        $I->fillField("AdminUser[roles]", "");
        $I->submitForm("button[type=submit]", ["AdminUser[permissions][sdfsf:POST]"=>0]);

        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('admin-user/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("success");
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $I->click("a[title=编辑]");
        $I->fillField("AdminUser[email]", 'update@feehi.com');
        $I->submitForm("button[type=submit]", ["AdminUser[permissions][sdfsf:POST]"=>0]);
        $I->seeInField("AdminUser[email]", "update@feehi.com");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see("头像");
    }
}
