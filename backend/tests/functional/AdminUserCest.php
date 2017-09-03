<?php

namespace backend\tests\functional;

use backend\models\User;
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
        $I->amLoggedInAs(User::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $I->see('管理员');
        $I->see("用户名");
        $I->click("a[title=编辑]");
        $I->see("编辑管理员");
        $I->fillField("User[username]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("User[username]", "123");
    }

    public function checkAssign(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-user/index'));
        $I->see('管理员');
        $I->see("用户名");
        $I->click("a[title=assignment]");
        $I->see("超级管理员");
    }
}
