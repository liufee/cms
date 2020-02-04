<?php

namespace backend\tests\functional;

use common\models\AdminUser;
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
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/index'));
        $I->see('分类');
        $I->see("别名");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/create'));
        $I->fillField("Category[name]", '分类名称');
        $I->fillField("Category[alias]", 'test_alias');
        $I->submitForm("button[type=submit]", []);
        $I->see("test_alias");
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/index'));
        $I->click("a[title=编辑]");
        $I->see("编辑分类");
        $I->fillField("Category[name]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Category[name]", "123");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('分类模板');
    }

    public function checkSort(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = "sort[" . json_encode(['id' => $data['id']]) . "]";//echo $key;exit;
        $I->sendAjaxPostRequest(Url::toRoute('category/sort'), [
            $key => 1,
        ]);
        $I->see("success");
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/category/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('category/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("422");
    }

}
