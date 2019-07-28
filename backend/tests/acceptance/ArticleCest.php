<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:37
 */
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

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

    public function _before(AcceptanceTester $I)
    {
        login($I);
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/article/index'));
        $I->see('标题');
        $I->see("作者");
        $I->click("a[title=编辑]");
        $I->see("编辑文章");
        $I->fillField("Article[summary]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Article[summary]", "123");
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/article/view-layer', 'id'=>22]));
        $I->see('查看');
    }

}