<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class HomeCest
{
    public function checkOpen(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/article/index'));
        $I->see('最新发布');
        $I->seeLink('关于我们');
    }
}