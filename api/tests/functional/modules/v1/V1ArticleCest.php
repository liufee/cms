<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:17
 */

namespace api\tests\functional;

use api\tests\FunctionalTester;


class V1ArticleCest
{
    public function checkIndex(FunctionalTester $I)
    {
        $I->sendGET('/v1/articles');
        $I->haveHttpHeader("X-Pagination-Current-Page", 1);
    }

    public function checkView(FunctionalTester $I)
    {
        $I->sendGET('/v1/articles/1');
        $I->seeResponseContains("title");
        $I->seeResponseContains("description");
    }
}