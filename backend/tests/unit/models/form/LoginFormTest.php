<?php
namespace backend\tests\unit\models;

use yii;
use backend\models\form\LoginForm as LoginFormOrigin;
use backend\fixtures\UserFixture;

class LoginFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {

    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'User.php'
            ]
        ];
    }

    // tests
    public function testLoginNoUser()
    {
        $model = new LoginForm([
            "username" => "not_exists_backend_username",
            "password" => "not_exists_backend_password",
        ]);
        expect("backend loginForm model should not login user", $model->login())->false();
        expect("backend user should not logged in", yii::$app->getUser()->getIsGuest())->true();
    }

    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'username' => 'backend.tester',
            'password' => 'wrong_password',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('error message should be set', $model->errors)->hasKey('password');
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'username' => 'bayer.hudson',
            'password' => 'password_0',
        ]);
        expect('model should login user', $model->login())->true();
        expect('error message should not be set', $model->errors)->hasntKey('password');
        expect('user should be logged in', Yii::$app->user->isGuest)->false();
    }

}

class LoginForm extends LoginFormOrigin{
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
}