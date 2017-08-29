<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class PageCest
{
    public function checkAbout(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/page/view', 'id'=>23]));
        $I->see('关于我们');
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/page/view', 'id'=>24]));
        $I->see('联系我们');
    }
}
