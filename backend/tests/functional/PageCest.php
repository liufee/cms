<?php

namespace backend\tests\functional;

use common\models\AdminUser;
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
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkSort(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = "article[" . json_encode(['id' => $data['id']]) . "]";//echo $key;exit;
        $I->sendAjaxPostRequest(Url::toRoute('page/sort'), [
            $key => 1,
        ]);
        $I->see("success");
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('article/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("success");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/create'));
        $I->fillField("Article[title]", 'test_title');
        $I->submitForm("button[type=submit]", []);
        $I->see("test_title");
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $I->click("a[title=编辑]");
        $I->see("编辑单页");
        $I->fillField("Article[summary]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Article[summary]", "123");
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $I->see('联系方式');
        $I->see("关于我们");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/page/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('创建时间');
    }
}
