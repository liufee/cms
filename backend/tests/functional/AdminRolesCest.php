<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class AdminRolesCest
 */
class AdminRolesCest
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
        $I->amOnPage(Url::toRoute('/admin-roles/index'));
        $I->see('角色');
        $I->see("超级管理员");
        $I->click("a[title=编辑]");
        $I->see("编辑角色");
    }

    public function checkAssign(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/admin-roles/index'));
        $I->see('角色');
        $I->see("超级管理员");
        $I->click("a[title=assignment]");
        $I->see("分配权限");
    }
}
