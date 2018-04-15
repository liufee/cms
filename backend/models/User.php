<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use backend\components\CustomLog;
use common\helpers\Util;
use Yii;
use yii\base\Event;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use \yii\web\ForbiddenHttpException;

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

    public $roles;

    public $permissions;


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

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => ['username', 'email'],
            'create' => ['username', 'email', 'password', 'avatar', 'status', 'roles', 'permissions'],
            'update' => ['username', 'email', 'password', 'avatar', 'status', 'roles', 'permissions'],
            'self-update' => ['username', 'email', 'password', 'avatar', 'old_password', 'repassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => yii::t('app', 'Username'),
            'email' => yii::t('app', 'Email'),
            'old_password' => yii::t('app', 'Old Password'),
            'password' => yii::t('app', 'Password'),
            'repassword' => yii::t('app', 'Repeat Password'),
            'avatar' => yii::t('app', 'Avatar'),
            'status' => yii::t('app', 'Status'),
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

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => yii::t('app', 'Normal'),
            self::STATUS_DELETED => yii::t('app', 'Disabled'),
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

    /**
     * @inheritdoc
     */
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
        $authManager = yii::$app->getAuthManager();
        if(!$this->getIsNewRecord() && in_array($this->id, yii::$app->getBehavior('access')->superAdminUserIds)){
            $this->permissions = $this->roles = [];
        }
        $assignments = $authManager->getAssignments($this->id);
        $roles = $permissions = [];
        foreach ($assignments as $key => $assignment){
            if( strpos($assignment->roleName, ':GET') || strpos($assignment->roleName, ':POST') ){
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
                $this->addError('old_password', yii::t('yii', '{attribute} cannot be blank.', ['attribute' => yii::t('app', 'Old Password')]));
                return false;
            }
            if (! $this->validatePassword($this->old_password)) {
                $this->addError('old_password', yii::t('app', '{attribute} is incorrect.', ['attribute' => yii::t('app', 'Old Password')]));
                return false;
            }
            if ($this->repassword != $this->password) {
                $this->addError('repassword', yii::t('app', '{attribute} is incorrect.', ['attribute' => yii::t('app', 'Repeat Password')]));
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
            throw new ForbiddenHttpException(yii::t('app', "Not allowed to delete {attribute}", ['attribute' => yii::t('app', 'default super administrator admin')]));
        }
        return true;
    }

    public function getRolesName()
    {
        if( in_array( $this->getId(), yii::$app->getBehavior('access')->superAdminUserIds ) ){
            return [yii::t('app', 'System')];
        }
        $role = array_keys( yii::$app->getAuthManager()->getRolesByUser($this->getId()) );
        if( !isset( $role[0] ) ) return [];
        return $role;
    }

    public function getRolesNameString($glue=',')
    {
        $roles = $this->getRolesName();
        $str = '';
        foreach ($roles as $role){
            $str .= yii::t('menu', $role) . $glue;
        }
        return rtrim($str, $glue);
    }

}

