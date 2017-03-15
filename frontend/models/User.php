<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 10:30
 */

namespace frontend\models;

use yii;
use common\libs\File;

class User extends \common\models\User
{

    public $password;
    public $repassword;
    public $old_password;

    public function rules()
    {
        return [
            [['username', 'password', 'repassword', 'password_hash', 'avatar'], 'string'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            [['repassword'], 'compare', 'compareAttribute' => 'password'],
            [['username', 'email', 'password', 'repassword'], 'required', 'on' => ['create']],
            [['username', 'email'], 'required', 'on' => ['update']],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['username', 'email', 'password', 'avatar', 'repassword'],
            'update' => ['username', 'email', 'password', 'repassword', 'avatar'],
            'self-update' => ['username', 'email', 'password', 'repassword', 'old_password', 'avatar'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => yii::t('app', 'Username'),
            'email' => yii::t('app', 'Email'),
            'old_password' => yii::t('app', 'Old Password'),
            'password' => yii::t('app', 'Password'),
            'repassword' => yii::t('app', 'Repeat Password'),
            'avatar' => yii::t('app', 'Avatar'),
            'created_at' => yii::t('app', 'Created At'),
            'updated_at' => yii::t('app', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = time();
            $this->generateAuthKey();
            $this->setPassword($this->password);
            $this->updated_at = 0;
        } else {
            $this->updated_at = time();
            if (isset($this->password) && $this->password != '') {
                if ($this->getScenario() == 'self-update') {
                    if ($this->old_password == '') {
                        $this->addError('old_password', 'Old password cannot be blank.');
                        return false;
                    }
                    if (! $this->validatePassword($this->old_password)) {
                        $this->addError('old_password', 'Old password is incorrect.');
                        return false;
                    }
                } else {
                    if ($this->getScenario() == 'update') {
                        if ($this->repassword == '') {
                            $this->addError('repassword', 'repassword cannot be blank.');
                            return false;
                        }
                    }
                }
                $this->setPassword($this->password);
            }
        }
        if ($_FILES['User']['name']['avatar'] != '') {
            $file = new File();
            $imgs = $file->upload(Yii::getAlias('@frontend/web/uploads/avatar'));
            if ($imgs[0] == false) {
                $this->addError('avatar', yii::t('app', 'Upload {attribute} error', ['attribute' => yii::t('app', 'avatar')]) . ': ' . $file->getErrors());
                return false;
            }
            $this->avatar = str_replace(Yii::getAlias('@frontend/web'), '', $imgs[0]);
            $oldAvatar = $this->getOldAttribute('avatar');
            if ($oldAvatar != '') {
                @unlink(yii::getAlias("@frontend/web") . $oldAvatar);
            }
        }
        if ($this->avatar == '') {
            unset($this->avatar);
        }
        return true;
    }

}