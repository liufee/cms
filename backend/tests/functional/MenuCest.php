<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class MenuCest
 */
class MenuCest
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
        $I->amOnPage(Url::toRoute('/menu/index'));
        $I->see('名称');
        $I->see("图标");
        $I->click("a[title=编辑]");
        $I->see("编辑后台菜单");
        $I->fillField("Menu[name]", '测试菜单1212');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Menu[name]", "测试菜单1212");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/menu/view-layer', 'id'=>24]));
        $I->see('父分类菜单名称');
        $I->see("名称");
    }
}
