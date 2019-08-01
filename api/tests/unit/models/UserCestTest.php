<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-08-02 00:31
 */

namespace api\tests\unit\models;


use api\fixtures\UserFixture;
use api\models\User;

class UserCestTest extends \Codeception\Test\Unit
{
    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'login_data.php'
            ]
        ];
    }

    protected function _before()
    {

    }

    protected function _after()
    {
    }


    public function testGenerateAccessToken(){
        $user = new User();
        $user->generateAccessToken();
        expect('generate token success', $user->access_token)->notEmpty();
    }
}