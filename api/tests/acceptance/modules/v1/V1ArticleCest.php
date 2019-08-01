<?php
namespace api\tests;

use api\tests\AcceptanceTester;

class V1ArticleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->sendGET('/v1/articles');
        $I->haveHttpHeader("X-Pagination-Current-Page", 1);
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->sendGET('/v1/articles/1');
        $I->seeResponseContains("title");
        $I->seeResponseContains("description");
    }
}
