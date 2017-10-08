<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class FriendlyLinkCest
 */
class FriendlyLinkCest
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
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $I->see('友情链接');
        $I->see("地址");
        $I->click("a[title=编辑]");
        $I->see("编辑友情链接");
        $I->fillField("FriendlyLink[name]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("FriendlyLink[name]", "123");
    }
}
