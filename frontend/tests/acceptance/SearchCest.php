<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class SearchCest
{
    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/search/index', 'q'=>'php']));
        $I->see('10个免费下载PHP脚本的网站');
    }
}
