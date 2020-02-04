<?php

namespace backend\tests\functional;

use common\models\AdminUser;
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
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/index'));
        $I->see('后台菜单');
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/index'));
        $I->click("a[title=编辑]");
        $I->see("编辑后台菜单");
        $I->fillField("Menu[name]", '测试123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Menu[name]", "测试123");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/create'));
        $I->fillField("Menu[name]", 'test_menu_name');
        $I->submitForm("button[type=submit]", []);
        $I->see("test_menu_name");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('创建时间');
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('menu/delete'), [
            'id' => $data['id'],
        ]);
        $I->see(422);
    }

    public function checkSort(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/menu/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = "sort[" . json_encode(['id' => $data['id']]) . "]";//echo $key;exit;
        $I->sendAjaxPostRequest(Url::toRoute('menu/sort'), [
            $key => 1,
        ]);
        $I->see("success");
    }
}
