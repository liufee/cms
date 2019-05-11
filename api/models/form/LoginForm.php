<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-05-10 23:39
 */
namespace api\models\form;

use api\models\User;

    /**
     * Login form
     */
class LoginForm extends \yii\base\Model
{
    public $username;
    public $password;

    /** @var User */
    private $_user;


    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }


    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();
            if (!$this->_user) {
                $this->addError($attribute, '用户名不存在');
                return false;
            }
            if( !$this->_user->validatePassword($this->password) ){
                $this->addError($attribute, '密码错误');
                return false;
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
        ];
    }

    public function login()
    {
        if ($this->validate()) {
            $this->_user->generateAccessToken();
            if( $this->_user->save(false, ['access_token']) ){
                return $this->_user;
            }else{
                return "false";
            }
        } else {
            return null;
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

}