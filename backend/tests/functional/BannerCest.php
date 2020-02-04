<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-07 19:31
 */
namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class BannerCest
 */
class BannerCest
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

    public function checkBannerTypeIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->see('Banner类型');
    }

    public function checkBannerTypeUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=编辑]");
        $I->see("编辑Banner类型");
        $I->fillField("BannerTypeForm[tips]", 'banner类型描述');
        $I->submitForm("button[type=submit]", []);
        $I->see('Banner类型');
        $I->see("描述");
    }

    public function checkBannerTypeCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/create'));
        $I->fillField("BannerTypeForm[name]", 'test_banner_type');
        $I->fillField("BannerTypeForm[tips]", 'description');
        $I->submitForm("button[type=submit]", []);
        $I->see("test_banner_type");
    }

    public function checkBannerTypeDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $urls = $I->grabMultiple("table a[title=进入]", "href");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('banner/delete'), [
            'id' => $data['id'],
        ]);
        $I->see(422);
    }

    public function checkBanners(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->see("Banner类型");
    }

    public function checkBannerCreate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $I->click("a[title=创建]");
        $I->fillField("BannerForm[link]", 'https://www.feehi.com');
        $I->fillField("BannerForm[desc]", '我是描述信息');
        $I->submitForm("button[type=submit]", []);
        $I->see("https://www.feehi.com");
    }

    public function checkBannerUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $I->click("a[title=编辑]");
        $I->fillField("BannerForm[desc]", 'banner图片描述');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("BannerForm[desc]", "banner图片描述");
    }

    public function checkBannerView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see("标识");
    }

    public function checkBannerDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $urls = $I->grabMultiple("table a[title=删除]", "url");
        $I->sendAjaxPostRequest($urls[0], [
        ]);
        $I->see("success");
    }

    public function checkBannerSort(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $urls = $I->grabMultiple("table a[title=删除]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $key = "sort[" . json_encode(['id' => $data['id'], 'sign' => $data['sign']]) . "]";
        $I->sendAjaxPostRequest(Url::toRoute(['banner/banner-sort', 'id'=>$data['id']]), [
            $key => 2,
        ]);
        $I->see("success");
    }
}
