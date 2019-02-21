<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;

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
        $I->submitForm('#form-login', $this->formParams('', ''));
        $I->seeValidationError('用户名不能为空');
        $I->seeValidationError('密码不能为空');
    }

    public function checkWrongPassword(FunctionalTester $I)
    {
        $I->submitForm('#form-login', $this->formParams('admin', 'wrong'));
        $I->seeValidationError('用户名');
    }
    
    public function checkValidLogin(FunctionalTester $I)
    {
        $I->submitForm('#form-login', $this->formParams('erau', 'password_0'));
        $I->see('退出登录');
        $I->dontSeeLink('Login');
        $I->dontSeeLink('Signup');
    }
}
