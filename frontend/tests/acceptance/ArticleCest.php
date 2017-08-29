<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class ArticleCest
{
    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/article/index'));
        $I->see('精选导读');

        $I->seeLink('关于我们');
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute(['/article/view', 'id'=>22]));
        $I->see('转载请注明');

        $I->seeLink('Feehi CMS');
    }
}
