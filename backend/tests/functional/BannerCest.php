<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-07 19:31
 */
namespace backend\tests\functional;

use backend\models\User;
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
        $I->amLoggedInAs(User::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->see('Banner类型');
        $I->see("描述");
        $I->click("a[title=编辑]");
        $I->see("编辑Banner类型");
        $I->fillField("BannerTypeForm[tips]", 'banner类型描述');
        $I->submitForm("button[type=submit]", []);
        $I->seeInField("BannerTypeForm[tips]", "banner类型描述");
    }

    public function checkBanners(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/banner/index'));
        $I->click("a[title=进入]");
        $I->click("a[title=编辑]");
        $I->fillField("BannerForm[desc]", 'banner图片描述');
        $I->submitForm("button[type=submit]", []);
        //$I->click("a[title=编辑]");
        $I->seeInField("BannerForm[desc]", "banner图片描述");
    }
}
