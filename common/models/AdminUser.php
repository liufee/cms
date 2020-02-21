<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use backend\components\CustomLog;
use common\helpers\Util;
use yii\base\Event;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property string $avatar
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class AdminUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $roles;

    public $permissions;

    public $password;

    public $repassword;

    public $old_password;


    /**
     * 返回数据表名
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%admin_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'repassword', 'password_hash'], 'string'],
            ['email', 'email'],
            ['email', 'unique'],
            [['repassword'], 'compare', 'compareAttribute' => 'password'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'email', 'password', 'repassword'], 'required', 'on' => ['create']],
            [['username', 'email'], 'required', 'on' => ['update', 'self-update']],
            [['username'], 'unique', 'on' => 'create'],
            [['roles', 'permissions'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => ['username', 'email'],
            'create' => ['username', 'email', 'password', 'avatar', 'status', 'roles', 'permissions'],
            'update' => ['username', 'email', 'password', 'avatar', 'status', 'roles', 'permissions'],
            'self-update' => ['email', 'password', 'avatar', 'old_password', 'repassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'old_password' => Yii::t('app', 'Old Password'),
            'password' => Yii::t('app', 'Password'),
            'repassword' => Yii::t('app', 'Repeat Password'),
            'avatar' => Yii::t('app', 'Avatar'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At')
        ];
    }

    public function beforeValidate()
    {
        if($this->avatar !== "0") {//为0表示需要删除图片，Util::handleModelSingleFileUpload()会有判断删除图片
            $this->avatar = UploadedFile::getInstance($this, "avatar");
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        Util::handleModelSingleFileUpload($this, 'avatar', $insert, '@admin/uploads/avatar/');
        if ($insert) {
            $this->generateAuthKey();
            $this->setPassword($this->password);
        } else {
            if (isset($this->password) && $this->password != '') {
                $this->setPassword($this->password);
            }
        }
        return parent::beforeSave($insert);
    }

    public function assignPermission()
    {
        $authManager = Yii::$app->getAuthManager();
        if(!$this->getIsNewRecord() && in_array($this->id, Yii::$app->getBehavior('access')->superAdminUserIds)){
            $this->permissions = $this->roles = [];
        }
        $assignments = $authManager->getAssignments($this->id);
        $roles = $permissions = [];
        foreach ($assignments as $key => $assignment){
            if( strpos($assignment->roleName, ':GET') || strpos($assignment->roleName, ':POST') || strpos($assignment->roleName, ':DELETE') ){
                $permissions[$key] = $assignment;
            }else{
                $roles[$key] = $assignment;
            }
        }
        $roles = array_keys($roles);
        $permissions = array_keys($permissions);

        $str = '';

        //角色roles
        if( !is_array( $this->roles ) ) $this->roles = [];

        $needAdds = array_diff($this->roles, $roles);
        $needRemoves = array_diff($roles, $this->roles);
        if( !empty($needAdds) ) {
            $str .= " 增加了角色: ";
            foreach ($needAdds as $role) {
                $roleItem = $authManager->getRole($role);
                $authManager->assign($roleItem, $this->id);
                $str .= " {$roleItem->name},";
            }
        }
        if( !empty($needRemoves) ) {
            $str .= ' 删除了角色: ';
            foreach ($needRemoves as $role) {
                $roleItem = $authManager->getRole($role);
                $authManager->revoke($roleItem, $this->id);
                $str .= " {$roleItem->name},";
            }
        }

       //权限permission
        $this->permissions = array_flip($this->permissions);
        if (isset($this->permissions[0])) unset($this->permissions[0]);
        $this->permissions = array_flip($this->permissions);

        $needAdds = array_diff($this->permissions, $permissions);
        $needRemoves = array_diff($permissions, $this->permissions);
        if( !empty($needAdds) ) {
            $str .= ' 增加了权限: ';
            foreach ($needAdds as $permission) {
                $permissionItem = $authManager->getPermission($permission);
                $authManager->assign($permissionItem, $this->id);
                $str .= " {$permissionItem->name},";
            }
        }
        if( !empty($needRemoves) ) {
            $str .= ' 删除了权限: ';
            foreach ($needRemoves as $permission) {
                $permissionItem = $authManager->getPermission($permission);
                $authManager->revoke($permissionItem, $this->id);
                $str .= " {$permissionItem->name},";
            }
        }

        Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
            'sender' => $this,
            'description' => "修改了 用户(uid {$this->id}) {$this->username} 的权限: {$str}",
        ]));

        return true;

    }

    /**
     * @inheritdoc
     */
    public function selfUpdate()
    {
        if ($this->password != '') {
            if ($this->old_password == '') {
                $this->addError('old_password', Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => Yii::t('app', 'Old Password')]));
                return false;
            }
            if (! $this->validatePassword($this->old_password)) {
                $this->addError('old_password', Yii::t('app', '{attribute} is incorrect.', ['attribute' => Yii::t('app', 'Old Password')]));
                return false;
            }
            if ($this->repassword != $this->password) {
                $this->addError('repassword', Yii::t('app', '{attribute} is incorrect.', ['attribute' => Yii::t('app', 'Repeat Password')]));
                return false;
            }
        }
        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if ($this->id == 1) {
            throw new ForbiddenHttpException(Yii::t('app', "Not allowed to delete {attribute}", ['attribute' => Yii::t('app', 'default super administrator admin')]));
        }
        return parent::beforeDelete();
    }

    public function getRolesName()
    {
        if( in_array( $this->getId(), Yii::$app->getBehavior('access')->superAdminUserIds ) ){
            return [Yii::t('app', 'System')];
        }
        $role = array_keys( Yii::$app->getAuthManager()->getRolesByUser($this->getId()) );
        if( !isset( $role[0] ) ) return [];
        return $role;
    }

    public function getRolesNameString($glue=',')
    {
        $roles = $this->getRolesName();
        $str = '';
        foreach ($roles as $role){
            $str .= Yii::t('menu', $role) . $glue;
        }
        return rtrim($str, $glue);
    }

    public function getAvatarUrl(){
        $avatarUrl = "";
        if ($this->avatar) {
            $avatarUrl = Yii::$app->params['site']['url'] . $this->avatar;
        } else {
            $avatarUrl = Yii::$app->getRequest()->getBaseUrl() . '/static/img/profile_small.jpg';
        }
        return $avatarUrl;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'Normal'),
            self::STATUS_DELETED => Yii::t('app', 'Disabled'),
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
        if (! static::isPasswordResetTokenValid($token)) {
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

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
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
}

