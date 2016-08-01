<?php
namespace backend\models;

use feehi\libs\File;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $password;
    public $repassword;
    public $old_password;

    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    public function rules()
    {
        return [
            [['username','password','repassword','password_hash','avatar'], 'string'],
            ['email', 'email'],
            [['repassword'], 'compare','compareAttribute'=>'password'],
            [['username','email','password', 'repassword'], 'required', 'on'=>['create']],
            [['username','email'], 'required', 'on'=>['update', 'self-update']],
            [['username'], 'unique', 'on'=>'create'],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['username', 'email', 'password', 'avatar'],
            'update' => ['username', 'email', 'password', 'avatar'],
            'self-update' => ['username', 'email', 'password', 'avatar', 'old_password', 'repassword'],
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
            'updated_at' => yii::t('app', 'Updated At')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
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
                $this->setPassword($this->password);
            }
        }
        if($this->avatar == '') unset($this->avatar);
        if(!$this->saveAvatar($insert)){
            $this->addError('avatar', 'save avatar failed');
        }
        return true;
    }

    private function saveAvatar()
    {
        if($_FILES['User']['name']['avatar'] == '') return true;
        $file = new File();
        $imgs = $file->upload(Yii::getAlias('@avatar'));
        if($imgs[0] == false){
            return false;
        }
        $oldAvatar = $this->getOldAttribute('avatar');
        if($oldAvatar != '') @unlink(yii::getAlias("@frontend/web").$oldAvatar);
        $this->avatar = str_replace(yii::getAlias('@frontend/web'), '', $imgs[0]);
        return true;
    }

    public function self_update()
    {
        if($this->password != '') {
            if ($this->old_password == '') {
                $this->addError('old_password', 'Old password cannot be blank.');
                return false;
            }
            if (!$this->validatePassword($this->old_password)) {
                $this->addError('old_password', 'Old password is incorrect.');
                return false;
            }
            if($this->repassword != $this->password){
                $this->addError('repassword', 'Repeat password not equaled password.');
                return false;
            }
        }
        return $this->save();
    }

    public function beforeDelete()
    {
        if($this->id == 1) throw new \yii\web\ForbiddenHttpException(yii::t('app', "Not allowed to delete default super administrator admin"));
        return true;
    }

}

