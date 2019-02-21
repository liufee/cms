<?php

namespace backend\tests\functional;

use backend\tests\FunctionalTester;
use backend\fixtures\UserFixture;

/**
 * Class LoginCest
 */
class LoginCest
{

    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
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
        $I->amOnRoute('site/login');
    }

    protected function formParams($login, $password)
    {
        return [
            'LoginForm[username]' => $login,
            'LoginForm[password]' => $password,
        ];
    }

    public function checkEmpty(FunctionalTester $I)
    {
        $I->submitForm('button[name=login-button]', $this->formParams('', ''));
        $I->see('用户名不能为空');
        $I->see('密码不能为空');
    }

    public function checkWrongPassword(FunctionalTester $I)
    {
        $I->submitForm('button[name=login-button]', $this->formParams('admin', 'wrong'));
        $I->see('用户名或密码错误');
    }

    public function checkValidLogin(FunctionalTester $I)
    {
        $I->submitForm('button[name=login-button]', array_merge($this->formParams('admin', 'password_0'), ['LoginForm[captcha]'=>'testme']));
        $I->see('菜单');
        $I->dontSeeLink('登录');
        $I->dontSeeLink('注册');
    }
}
