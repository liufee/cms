<?php

namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use yii\helpers\Url;

/**
 * Class SiteCest
 */
class SiteCest
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

    public function checkCaptcha(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/captcha'));
        $I->seeResponseCodeIs(200);
    }

    public function checkLogout(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/logout'));
        $I->seeResponseCodeIs(405);
    }

    public function checkLanguage(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/index'));
        $I->amOnPage(Url::toRoute(['/site/language', 'lang'=>'en-US']));
        $I->see("System");
    }

    public function checkError(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/error'));
        $I->see("404");
    }

}
