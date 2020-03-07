<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:37
 */
namespace backend\tests\acceptance;

use backend\fixtures\UserFixture;
use backend\tests\AcceptanceTester;
use yii\helpers\Url;

class LogCest
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

    public function _before(AcceptanceTester $I)
    {
        login($I);
    }

    public function checkIndex(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/log/index'));
        $I->see('日志');
        $I->see("管理员");
    }

    public function checkView(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/log/index'));
        $urls = $I->grabMultiple("table a[title=查看]", "url");
        $I->amOnPage($urls[0]);
        $I->see('创建时间');
        $I->see('管理员');
    }

}