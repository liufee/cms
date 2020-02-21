<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use yii\helpers\Url;

class SearchCest
{
    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/search/index', 'q'=>'php']));
        $I->see('10个免费下载PHP脚本的网站');
    }

    public function checkTag(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/search/tag', 'q'=>'php']));
        $I->see('Tag');
    }
}
