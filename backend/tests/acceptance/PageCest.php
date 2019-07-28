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

    public function _before(AcceptanceTester $I)
    {
        login($I);
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $I->see('联系方式');
        $I->see("关于我们");
        $I->click("a[title=编辑]");
        $I->see("编辑单页");
        $I->fillField("Article[summary]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Article[summary]", "123");
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/page/view-layer', 'id'=>24]));
        $I->see('标题');
        $I->see("副标题");
    }

}