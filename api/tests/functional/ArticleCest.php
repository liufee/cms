<?php

namespace api\tests\functional;

use api\tests\FunctionalTester;

class ArticleCest
{
    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage('/articles');
        $I->haveHttpHeader("X-Pagination-Current-Page", 1);
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage('/articles/1');
        $I->see("title");
        $I->see("description");
    }
}