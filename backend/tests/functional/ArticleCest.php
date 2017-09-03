<?php

namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class ArticleCest
 */
class ArticleCest
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
        $I->amOnPage(Url::toRoute('/article/index'));
        $I->see('标题');
        $I->see("作者");
        $I->click("a[title=编辑]");
        $I->see("编辑文章");
        $I->fillField("Article[summary]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("Article[summary]", "123");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/article/index', 'id'=>22]));
        $I->see('查看');
    }
}
