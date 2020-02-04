<?php

namespace backend\tests\functional;

use common\models\AdminUser;
use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;
use common\models\Comment;
use yii\helpers\Url;

/**
 * Class CommentCest
 */
class CommentCest
{

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(AdminUser::findIdentity(1));
    }

    public function checkIndex(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/comment/index'));
        $I->see('评论');
        $I->see("文章标题");
    }

    public function checkView(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/comment/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('IP地址');
    }

    public function checkUpdate(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/comment/index'));
        $I->click("a[title=编辑]");
        $I->fillField("Comment[nickname]", 'test_nickname');
        $I->submitForm("button[type=submit]", []);
        $I->click("a[title=编辑]");
        $I->seeInField("Comment[nickname]", "test_nickname");
    }

    public function checkAudit(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/article/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->sendAjaxPostRequest($urls[0], [
            'Comment[status]' => Comment::STATUS_UNPASS,
        ]);
        $I->sendAjaxPostRequest($urls[0], [
            'Comment[status]' => Comment::STATUS_PASSED,
        ]);
        $I->see("评论");
    }

    public function checkDelete(FunctionalTester $I)
    {
        $I->amOnPage(Url::toRoute('/article/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $data = \GuzzleHttp\Psr7\parse_query($urls[0]);
        $I->sendAjaxPostRequest(Url::toRoute('article/delete'), [
            'id' => $data['id'],
        ]);
        $I->see("success");
    }
}
