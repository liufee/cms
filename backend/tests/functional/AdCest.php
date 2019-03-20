<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-07 19:27
 */
namespace backend\tests\functional;

use backend\models\User;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class AdCest
 */
class AdCest
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
        $I->amOnPage(Url::toRoute('/ad/index'));
        $I->see('	广告');
        $I->see("	广告");
        $I->click("a[title=编辑]");
        $I->see("	编辑广告");
        $I->fillField("AdForm[desc]", '123广告描述');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("AdForm[desc]", "123广告描述");
    }
}
