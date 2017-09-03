<?php

namespace backend\tests\functional;

use backend\models\User;
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
        $I->amLoggedInAs(User::findIdentity(1));
        $I->amOnRoute('setting/website');
    }

    public function checkError(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/site/error'));
        $I->see("404");
    }

}
