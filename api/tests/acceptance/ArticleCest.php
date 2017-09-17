<?php
namespace api\tests;
use api\tests\AcceptanceTester;
use yii\helpers\Url;

class ArticleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage('/articles');
        $I->haveHttpHeader("X-Pagination-Current-Page", 1);
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage('/articles/1');
        $I->see("title");
        $I->see("description");
    }
}
