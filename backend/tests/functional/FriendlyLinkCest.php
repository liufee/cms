<?php

namespace backend\tests\functional;

use common\models\AdminUser;
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
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $I->see('友情链接');
        $I->see("地址");
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $I->click("a[title=编辑]");
        $I->see("编辑友情链接");
        $I->fillField("FriendlyLink[name]", '123');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("FriendlyLink[name]", "123");
    }

    public function checkCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/create'));
        $I->fillField("FriendlyLink[name]", 'test_name');
        $I->fillField("FriendlyLink[url]", 'https://wwww.feehi.com');
        $I->submitForm("button[type=submit]", []);
        $I->see("https://wwww.feehi.com");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('创建时间');
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('friendly-link/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("success");
    }

    public function checkSort(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/friendly-link/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = "sort[" . json_encode(['id' => $data['id']]) . "]";//echo $key;exit;
        $I->sendAjaxPostRequest(Url::toRoute('friendly-link/sort'), [
            $key => 1,
        ]);
        $I->see("success");
    }
}
