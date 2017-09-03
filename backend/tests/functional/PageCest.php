<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class PageCest
 */
class PageCest
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
        $I->amOnPage(Url::toRoute('/page/index'));
        $I->see('联系方式');
        $I->see("关于我们");
        $I->click("a[title=编辑]");
        $I->see("编辑单页");
        $I->fillField("Article[summary]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("Article[summary]", "123");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/page/index', 'id'=>24]));
        $I->see('查看');
        $I->see("关于我们");
    }
}
