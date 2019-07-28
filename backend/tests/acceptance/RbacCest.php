<?php
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class RbacCest
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

    public function checkPermissions(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/permissions'));
        $I->see('路由');
        $I->see("描述");
    }

    public function checkRoles(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/rbac/roles'));
        $I->see('角色');
        $I->see("描述");
    }

}