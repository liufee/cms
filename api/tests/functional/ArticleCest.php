<?php

namespace api\tests\functional;

use api\tests\FunctionalTester;

class ArticleCest
{
    public function checkIndex(FunctionalTester $I)
    {
        $I->sendGET('/articles');
        $I->haveHttpHeader("X-Pagination-Current-Page", 1);
    }

    public function checkView(FunctionalTester $I)
    {
        $I->sendGET('/articles/1');
        $I->canSeeResponseContains("title");
        $I->canSeeResponseContains("description");
    }
}