<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 13:53
 */
namespace backend\models\form;

use Yii;
use backend\components\CustomLog;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RbacForm extends \yii\base\Model
{
    public $name;

    public $type;

    public $description;

    public $ruleName;

    public $data;


    public $route;

    public $method;

    public $group;

    public $category;

    public $sort;


    public $permissions;

    public $roles;



    public function rules()
    {
        return [
            [['route', 'method', 'description', 'group', 'category'], 'required', 'on' => 'permission'],
            [['sort'], 'number', 'on'=>['role', 'permission']],
            [['sort'], 'default', 'value'=>0, 'on'=>['role', 'permission']],
            [
                ['route'],
                'match',
                'pattern' => '/^[\/].*/',
                'message' => Yii::t('app', Yii::t('app', 'Must begin with "/" like "/module/controller/action" format')),
                'on' => 'permission'
            ],

            [['name', 'description'], 'required', 'on' => 'role'],
            [['roles', 'permissions'], 'default', 'on' => 'role'],
        ];
    }

    public function scenarios()
    {
        return [
            'permission' => ['route', 'method', 'description', 'sort', 'group', 'category'],
            'role' => ['name', 'description', 'sort', 'roles', 'permissions']
        ];
    }

    public function attributeLabels()
    {
        return [
            "route" => Yii::t('app', 'Route'),
            "method" => Yii::t('app', 'HTTP Method'),
            "description" => Yii::t('app', 'Description'),
            "group" => Yii::t('app', 'Group'),
            "category" => Yii::t('app', 'Category'),
            "sort" => Yii::t('app', 'Sort'),
            "name" => Yii::t('app', 'Role'),
            "permissions" => Yii::t('app', 'Permissions'),
            "roles" => Yii::t('app', 'Role'),
        ];
    }

    public function createPermission()
    {
        $this->name = $this->route . ':' . $this->method;
        $authManager = Yii::$app->getAuthManager();
        if ($authManager->getPermission($this->name) !== null) {
            $this->addError('route', Yii::t('app', 'Permission exists'));
            $this->addError('method', Yii::t('app', 'Permission exists'));
            return false;
        }
        $permission = $authManager->createPermission($this->name);
        $permission->description = $this->description;
        $permission->data = json_encode([
            'group' => $this->group,
            'sort' => $this->sort,
            'category' => $this->category,
        ]);
         if( $authManager->add($permission) ){
             Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, new CustomLog([
                 'sender' => $this,
             ]));
             return true;
         }
         return false;
    }

    public function updatePermission($name)
    {
        $oldModel = clone $this;
        $oldModel->fillModel($name);
        $this->name = $this->route . ':' . $this->method;
        $authManager = Yii::$app->getAuthManager();
        $permission = $authManager->getPermission($name);
        if( $permission->name != $name ){//修改权限名称
            if( $authManager->getPermission($name) !== null ){
                $this->addError('route', Yii::t('app', 'Permission exists'));
                $this->addError('method', Yii::t('app', 'Permission exists'));
                return false;
            }
        }
        $permission->name = $this->name;
        $permission->description = $this->description;
        $permission->data = json_encode([
            'group' => $this->group,
            'sort' => $this->sort,
            'category' => $this->category,
        ]);
        if( $authManager->update($name, $permission) ){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'old' => $oldModel,
            ]));
            return true;
        }
        return false;
    }

    public function deletePermission()
    {
        $authManager = Yii::$app->getAuthManager();
        $permission = $authManager->getPermission($this->name);
        if( $authManager->remove($permission) ){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_DELETE, new CustomLog([
                'sender' => $this,
            ]));
            return true;
        }
        return false;
    }

    public function createRole()
    {
        $authManager = Yii::$app->getAuthManager();
        if ($authManager->getRole($this->name) !== null) {
            $this->addError('name', Yii::t('app', 'Role exists'));
            return false;
        }
        $role = $authManager->createRole($this->name);
        $role->description = $this->description;
        $role->data = json_encode([
            'sort' => $this->sort,
        ]);
        if( $authManager->add($role) ){
            if( $this->permissions === null ) $this->permissions = [];
            $this->permissions = array_flip($this->permissions);
            if (isset($this->permissions[0])) unset($this->permissions[0]);
            $this->permissions = array_flip($this->permissions);
            foreach ($this->permissions as $permission){
                $permission = $authManager->getPermission($permission);
                $authManager->addChild($role, $permission);
            }

            if( $this->roles === null ) $this->roles = [];
            $this->roles = array_flip($this->roles);
            if (isset($this->roles[0])) unset($this->roles[0]);
            $this->roles = array_flip($this->roles);
            foreach ($this->roles as $needAdd){
                $needAdd = $authManager->getRole($needAdd);
                $authManager->addChild($role, $needAdd);
            }

            Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, new CustomLog([
                'sender' => $this,
            ]));
            return true;
        }
        return false;
    }

    public function updateRole($name)
    {
        $oldModel = clone $this;
        $oldModel->fillModel($name);

        $authManager = Yii::$app->getAuthManager();
        $role = $authManager->getRole($name);
        if( $role->name != $this->name ){//修改角色名称
            if( $authManager->getRole($this->name) !== null ){
                $this->addError('name', Yii::t('app', 'Role exists'));
                return false;
            }
        }
        $role->name = $this->name;
        $role->description = $this->description;
        $role->data = json_encode([
            "sort" => $this->sort,
        ]);

        $oldPermissions = array_keys( $authManager->getPermissionsByRole($name) );
        $oldRoles = array_keys($authManager->getChildRoles($name));

        if( $authManager->update($name, $role) ){
            if( $this->permissions === null ) $this->permissions = [];
            $this->permissions = array_flip($this->permissions);
            if (isset($this->permissions[0])) unset($this->permissions[0]);
            $this->permissions = array_flip($this->permissions);

            $needAdds = array_diff($this->permissions, $oldPermissions);
            foreach ($needAdds as $permission){
                $permission = $authManager->getPermission($permission);
                $authManager->addChild($role, $permission);
            }

            $needRemoves = array_diff($oldPermissions, $this->permissions);
            foreach ($needRemoves as $permission){
                $permission = $authManager->getPermission($permission);
                $authManager->removeChild($role, $permission);
            }

            if( $this->roles === null ) $this->roles = [];
            $this->roles = array_flip($this->roles);
            if (isset($this->roles[0])) unset($this->roles[0]);
            $this->roles = array_flip($this->roles);
            $needAdds = array_diff($this->roles, $oldRoles);
            foreach ($needAdds as $needAdd){
                $needAdd = $authManager->getRole($needAdd);
                $authManager->addChild($role, $needAdd);
            }

            $needRemoves = array_diff($oldRoles, $this->roles);
            foreach ($needRemoves as $needRemove){
                $needRemove = $authManager->getRole($needRemove);
                if( !$needRemove ) continue;
                $authManager->removeChild($role, $needRemove);
            }

            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'old' => $oldModel,
            ]));
            return true;
        }
        return false;

    }

    public function deleteRole()
    {
        $authManager = Yii::$app->getAuthManager();
        $role = $authManager->getRole($this->name);
        if ($authManager->remove($role)) {
            Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_DELETE, new CustomLog([
                'sender' => $this,
            ]));
            return true;
        } else {
            return false;
        }
    }

    public function getPermissionsByGroup($type='index')
    {
        $authManager = Yii::$app->getAuthManager();
        $fillDatas = [];
        $originPermissions = $authManager->getPermissions();

        $permissions = [];
        foreach ($originPermissions as $originPermission){
            $data = json_decode($originPermission->data, true);
            $temp = explode(":", $originPermission->name);
            $permissions[] = [
                'name' => $originPermission->name,
                'route' => $temp[0],
                'method' => $temp[1],
                'description' => $originPermission->description,
                'group' => $data['group'],
                'category' => $data['category'],
                'sort' => $data['sort'],
            ];
        }
        ArrayHelper::multisort($permissions, 'sort');
        foreach ($permissions as $permission){
            $fillDatas[$permission['group']][$permission['category']][] = $permission;
        }
        $return = [];
        if( $type == 'index' ){
            foreach ($fillDatas as $value){
                foreach ($value as $val) {
                    foreach ($val as $v) {
                        $return[] = new self(array_merge($v, ['scenario' => 'role']));
                    }
                }
            }
        }else{
            $return = $fillDatas;
        }
        return $return;
    }

    public function getRoles()
    {
        $authManager = Yii::$app->getAuthManager();
        $originRoles = $authManager->getRoles();

        $fillDatas = [];
        foreach ($originRoles as $originRole){
            $data = json_decode($originRole->data, true);
            $fillDatas[] = [
                'name' => $originRole->name,
                'description' => $originRole->description,
                'sort' => $data['sort'],
            ];
        }

        ArrayHelper::multisort($fillDatas, 'sort');

        $return = [];
        foreach ($fillDatas as $fillData){

            $return[] = new self(array_merge($fillData, ['scenario'=>'role']));
        }
        return $return;
    }

    public function fillModel($name)
    {
        $authManager = Yii::$app->getAuthManager();
        if( $this->getScenario() == 'permission' ){
            $permission = $authManager->getPermission($name);
            if( $permission === null ) throw new NotFoundHttpException("Cannot find permission $name");
            $data = json_decode($permission->data, true);
            $temp = explode(":", $permission->name);
            $this->name = $permission->name;
            $this->route = $temp[0];
            $this->method = $temp[1];
            $this->description = $permission->description;
            $this->group = $data['group'];
            $this->category = isset( $data['category'] ) ? $data['category'] : '';
            $this->sort = $data['sort'];
        }else{
            $role = $authManager->getRole($name);//var_dump($name, $role);exit;
            $data = json_decode($role->data, true);
            $temp = $authManager->getPermissionsByRole($role->name);
            $permissions = [];
            foreach ($temp as $permission){
                $permissions[$permission->name] = $permission->name;
            }
            $this->name = $role->name;
            $this->description = $role->description;
            $this->sort = $data['sort'];
            $this->permissions = $permissions;
            $this->roles = array_keys( $authManager->getChildRoles($role->name) );
        }
    }

    public function getGroups()
    {
        $temp = $this->getPermissionsByGroup();
        $groups = [];
        foreach ($temp as $v){
            $groups[$v->group] = $v->group;
        }
        return $groups;
    }

    public function getCategories()
    {
        $temp = $this->getPermissionsByGroup();
        $categories = [];
        foreach ($temp as $v){
            $categories[$v->category] = $v->category;
        }
        return $categories;
    }


}