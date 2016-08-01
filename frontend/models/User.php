<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 10:30
 */
namespace frontend\models;

use yii;
use common\models\User as CommonUser;
use feehi\libs\File;

class User extends CommonUser
{

    public $password;
    public $repassword;
    public $old_password;
    public $avatar;

    public function rules()
    {
        return [
            [['username','password','repassword','password_hash','avatar'], 'string'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            [['repassword'], 'compare','compareAttribute'=>'password'],
            [['username','email','password', 'repassword'], 'required', 'on'=>['create']],
            [['username','email'], 'required', 'on'=>['update']],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['username', 'email', 'password','avatar', 'repassword'],
            'update' => ['username', 'email', 'password', 'repassword','avatar'],
            'self-update' => ['username', 'email', 'password', 'repassword', 'old_password', 'avatar'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'email' => '邮箱',
            'old_password' => '旧密码',
            'password' => '密码',
            'repassword' => '重复密码',
            'avatar' => '头像',
            'created_at' => '创建时间',
            'updated_at' => '最后修改'
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->created_at = time();
            $this->generateAuthKey();
            $this->setPassword($this->password);
            $this->updated_at = 0;
        }else{
            $this->updated_at = time();
            if(isset($this->password) && $this->password != ''){
                if( $this->getScenario() == 'self-update' ) {
                    if ($this->old_password == '') {
                        $this->addError('old_password', 'Old password cannot be blank.');
                        return false;
                    }
                    if (!$this->validatePassword($this->old_password)) {
                        $this->addError('old_password', 'Old password is incorrect.');
                        return false;
                    }
                }else if($this->getScenario() == 'update'){
                    if ($this->repassword == '') {
                        $this->addError('repassword', 'repassword cannot be blank.');
                        return false;
                    }
                }
                $this->setPassword($this->password);
            }
        }
        if($_FILES['User']['name']['avatar'] != ''){
            $file = new File();
            $imgs = $file->upload(Yii::getAlias('@webroot').yii::$app->params['uploadPath']['user']['avatar']);
            if($imgs[0] == false){
                return false;
            }
            $this->avatar = str_replace(Yii::getAlias('@webroot'), '', $imgs[0]);
        }
        if($this->avatar == '') unset($this->avatar);
        return true;
    }

}