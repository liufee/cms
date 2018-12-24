<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models;

use Yii;
use backend\components\CustomLog;
use common\helpers\Util;
use yii\base\Event;
use yii\web\ForbiddenHttpException;

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
class User extends \common\models\User
{

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

}

