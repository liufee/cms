<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class CategoryCest
 */
class CategoryCest
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
        $I->amOnPage(Url::toRoute('/category/index'));
        $I->see('分类');
        $I->see("别名");
        $I->click("a[title=编辑]");
        $I->see("编辑分类");
        $I->fillField("Category[name]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("Category[name]", "123");
    }
}
