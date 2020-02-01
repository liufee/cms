<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 17:19
 */

namespace common\services;

use Yii;
use backend\models\form\RBACPermissionForm;
use backend\models\form\RBACRoleForm;
use backend\models\search\RBACFormSearch;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class RBACService extends Service implements RBACServiceInterface
{

    const TYPE_PERMISSION = "permission";

    /** @var \yii\rbac\ManagerInterface */
    private $authManager;

    public function init()
    {
        parent::init();
        $this->authManager = \Yii::$app->getAuthManager();
    }

    public function getSearchModel(array $query, array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getModel($id, array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getNewModel(array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getNewPermissionModel()
    {
        return new RBACPermissionForm();
    }

    public function getPermissionList($query)
    {
        $searchModel = new RBACFormSearch(['scenario'=>'permission']);
        $dataProvider = $searchModel->searchPermissions($query);
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function createPermission(array $postData = [])
    {
        $formModel = $this->getNewPermissionModel();
        if ( !$formModel->load($postData) ){
            return $formModel->getErrors();
        }

        if ($this->authManager->getPermission($formModel->name) !== null) {
            $formModel->addError('route', Yii::t('app', 'Permission exists'));
            $formModel->addError('method', Yii::t('app', 'Permission exists'));
            return false;
        }

        $permission = $this->authManager->createPermission($formModel->getName());
        $permission->description = $formModel->description;
        $permission->data = $formModel->getData();
        if( $this->authManager->add($permission) ){
            /*Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, new CustomLog([
                'sender' => $this,
            ]));*/
            return true;
        }
        return false;
    }

    public function getPermissionDetail($name)
    {
        $permission = $this->authManager->getPermission($name);
        $formModel = new RBACPermissionForm();
        $formModel->setAttributes($permission);
        return $formModel;
    }

    public function updatePermission($name, array $postData = [])
    {
        $formModel = $this->getNewPermissionModel();
        if ( !$formModel->load($postData) ){
            return $formModel->getErrors();
        }

        $permission = $this->authManager->createPermission($formModel->getName());
        $permission->description = $formModel->description;
        $permission->data = $formModel->getData();
        if( $this->authManager->update($name, $permission) ){
            /*Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, new CustomLog([
                'sender' => $this,
            ]));*/
            return true;
        }
        return false;
    }

    public function deletePermission($name)
    {
        $permission = $this->authManager->createPermission($name);
        return $this->authManager->remove($permission);
    }

    public function sortPermission($name, $sort)
    {
        $permission = $this->authManager->getPermission($name);
        $formModel = $this->getNewPermissionModel();
        $formModel->setAttributes($permission);
        $formModel->sort = $sort;
        $permission->data = $formModel->getData();
        return $this->authManager->update($name, $permission);
    }



    public function getNewRoleModel()
    {
        return new RBACRoleForm();
    }

    public function getRoleList(array $query = [])
    {
        $searchModel = new RBACFormSearch(['scenario' => 'roles']);
        $dataProvider = $searchModel->searchRoles($query);
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function createRole(array $postData = [])
    {
        $formModel = $this->getNewRoleModel();
        if ( !$formModel->load($postData) ){
            return $formModel->getErrors();
        }

        if ($this->authManager->getRole($formModel->name) !== null) {
            $formModel->addError('name', Yii::t('app', 'Role exists'));
            return false;
        }

        $role = $this->authManager->createRole($formModel->name);
        $role->description = $formModel->description;
        $role->data = $formModel->getData();
        if( $this->authManager->add($role) ){

            //add role's permission
            $permissions = $formModel->getPermissions();
            foreach ($permissions as $permissionName){
                $permissionItem = $this->authManager->getPermission($permissionName);
                if( $permissionItem === null ) {
                    throw new Exception("Not existed permission name " . $permissionName);
                }
                $result = $this->authManager->addChild($role, $permissionItem);
                if(!$result){
                    return false;
                }
            }

            //add roles's child role
            $childRoles = $formModel->getRoles();
            foreach ($childRoles as $needAddChildRole){
                $childRole = $this->authManager->getRole($needAddChildRole);
                $result = $this->authManager->addChild($role, $childRole);
                if(!$result){
                    return false;
                }
            }

           /* Event::trigger(CustomLog::className(), CustomLog::EVENT_AFTER_CREATE, new CustomLog([
                'sender' => $this,
            ]));*/
            return true;
        }
        return false;
    }

    public function getRoleDetail($name)
    {
        $role = $this->authManager->getRole($name);
        $formModel = new RBACRoleForm();
        $formModel->setAttributes($role);
        $formModel->setPermissions( $this->authManager->getPermissionsByRole($name) );
        $formModel->setRoles( $this->authManager->getChildRoles($name) );
        return $formModel;
    }

    public function updateRole($name, array $postData = [])
    {
        $formModel = $this->getNewRoleModel();
        if ( !$formModel->load($postData) ){
            return $formModel->getErrors();
        }


        $role = $this->authManager->getRole($name);
        if( $name != $formModel->name ){//修改角色名称
            if( $this->authManager->getRole($formModel->name) !== null ){
                return Yii::t('app', 'Role exists');
            }
        }
        $role->name = $formModel->name;
        $role->description = $formModel->description;
        $role->data = $formModel->getData();

        $oldPermissions = array_keys( $this->authManager->getPermissionsByRole($name) );
        $oldRoles = array_keys($this->authManager->getChildRoles($name));

        if( $this->authManager->update($name, $role) ){
            $permissions = $formModel->getPermissions();
            $needAdds = array_diff($permissions, $oldPermissions);
            foreach ($needAdds as $permission){
                $permission = $this->authManager->getPermission($permission);
                $this->authManager->addChild($role, $permission);
            }

            $needRemoves = array_diff($oldPermissions, $formModel->getPermissions());
            foreach ($needRemoves as $permission){
                $permission = $this->authManager->getPermission($permission);
                $this->authManager->removeChild($role, $permission);
            }

            $roles = $formModel->getRoles();
            $needAdds = array_diff($roles, $oldRoles);
            foreach ($needAdds as $needAdd){
                $needAdd = $this->authManager->getRole($needAdd);
                $this->authManager->addChild($role, $needAdd);
            }

            $needRemoves = array_diff($oldRoles, $formModel->getRoles());
            foreach ($needRemoves as $needRemove){
                $needRemove = $this->authManager->getRole($needRemove);
                if( !$needRemove ) continue;
                $this->authManager->removeChild($role, $needRemove);
            }

         /*   Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'old' => $oldModel,
            ]));*/
            return true;
        }
        return false;
    }

    public function sortRole($name, $sort)
    {
        $role = $this->authManager->getRole($name);
        $formModel = $this->getNewRoleModel();
        $formModel->setAttributes($role);
        $formModel->sort = $sort;
        $role->data = $formModel->getData();
        return $this->authManager->update($name, $role);
    }

    public function deleteRole($name)
    {
        $role = $this->authManager->getRole($name);
        $permissions = $this->authManager->getPermissionsByRole($name);
        foreach ($permissions as $permission){
            $result = $this->authManager->remove($permission);
            if( !$result ){
                Yii::error("delete role remove permission " . $permission->name . " error");
            }
        }
        return $this->authManager->remove($role);
    }

    public function getRoles()
    {
        $roles = [];
        foreach (array_keys($this->authManager->getRoles()) as $key){
            $roles[$key] = $key;
        }
        return $roles;
    }

    public function getPermissionsGroups()
    {
        $authManager = $this->authManager;

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
        $data = [];
        foreach ($permissions as $permission){
            $data[$permission['group']][$permission['category']][] = $permission;
        }
        return $data;
    }

    public function getPermissionGroups()
    {
        $permissions = $this->getPermissionsGroups();
        return array_keys($permissions);
    }

    public function getPermissionCategories()
    {
        $permissions = $this->getPermissionsGroups();
        $categories = [];
        foreach ($permissions as $permission){
            $categories = array_merge($categories, array_keys($permission));
        }
        return $categories;
    }
}