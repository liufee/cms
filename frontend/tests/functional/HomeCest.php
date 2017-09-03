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

    public function checView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute(['/article/view', 'id'=>'19']));
        $I->seeInTitle("Java 8最快的垃圾搜集器是什么？");
    }
}