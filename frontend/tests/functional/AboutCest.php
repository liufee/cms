<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class AboutCest
{
    public function checkAbout(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/page/view', 'id'=>23]));
        $I->see('关于我们');
    }
}
