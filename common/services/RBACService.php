<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2020-01-29 17:19
 */

namespace common\services;

use Yii;
use backend\components\CustomLog;
use backend\models\form\RBACPermissionForm;
use backend\models\form\RBACRoleForm;
use yii\base\Event;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class RBACService extends Service implements RBACServiceInterface
{

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

    public function newModel(array $options = [])
    {
        throw new Exception("Not need");
    }

    public function getNewPermissionModel()
    {
        return new RBACPermissionForm();
    }

    public function getPermissionList(array $query = [])
    {
        $items = $this->authManager->getPermissions();
        $permissions = [];
        foreach ($items as $item){
            $model = new RBACPermissionForm();
            $model->setAttributes($item);
            $permissions[] = $model->getAttributes();
        }
        ArrayHelper::multisort($permissions, 'sort');
        $sortedPermissions = [];
        foreach ($permissions as $item){
            $model = new RBACPermissionForm();
            $model->setAttributes($item);
            $sortedPermissions[] = $model;
        }
        //$searchModel = new RBACFormSearch(['scenario'=>'permission']);
        //$dataProvider = $searchModel->searchPermissions($query);
        $dataProvider = new ArrayDataProvider([
                            'allModels' => $sortedPermissions,
                            'pagination' => [
                                'pageSize' => -1,
                            ]
                        ]);
        return [
            'dataProvider' => $dataProvider,
            //'searchModel' => $searchModel,
        ];
    }

    public function createPermission(array $postData = [])
    {
        $formModel = $this->getNewPermissionModel();
        if ( !$formModel->load($postData) ){
            return $formModel;
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
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $formModel,
                'description' => "创建 权限 (" . $formModel->getName() . ") " . print_r($formModel->getAttributes(), true),
            ]));
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

        $oldPermission = $this->getPermissionDetail($name);
        $permission = $this->authManager->createPermission($formModel->getName());
        $permission->description = $formModel->description;
        $permission->data = $formModel->getData();
        if( $this->authManager->update($name, $permission) ){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $formModel,
                'description' => "修改 权限(" . $name .  ") 从" . print_r($oldPermission, true) . "变更为" . print_r($formModel->getAttributes(), true),
            ]));
            return true;
        }
        return false;
    }

    public function deletePermission($name)
    {
        $permission = $this->authManager->createPermission($name);
        $result = $this->authManager->remove($permission);
        if( $result ){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'description' => "删除 权限(" . $name . ")",
            ]));
        }
        return $result;
    }

    public function sortPermission($name, $sort)
    {
        $permission = $this->authManager->getPermission($name);
        $formModel = $this->getNewPermissionModel();
        $formModel->setAttributes($permission);
        $oldSort = $formModel->sort;
        $formModel->sort = $sort;
        $permission->data = $formModel->getData();
        $result = $this->authManager->update($name, $permission);
        if($result){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'description' => "修改 权限(" . $name . ") 排序 " . $oldSort . "为" . $sort,
            ]));
        }
        return $result;
    }



    public function getNewRoleModel()
    {
        return new RBACRoleForm();
    }

    public function getRoleList(array $query = [])
    {
        $items = $this->authManager->getRoles();
        $roles = [];
        foreach ($items as $item){
            $model = new RBACRoleForm();
            $model->setAttributes($item);
            $roles[] = $model->getAttributes();
        }
        ArrayHelper::multisort($roles, 'sort');
        $sortedRoles = [];
        foreach ($roles as $item){
            $model = new RBACRoleForm();
            $model->setAttributes($item);
            $sortedRoles[] = $model;
        }
        //$searchModel = new RBACFormSearch(['scenario'=>'permission']);
        //$dataProvider = $searchModel->searchPermissions($query);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $sortedRoles,
            'pagination' => [
                'pageSize' => -1,
            ]
        ]);
        return [
            'dataProvider' => $dataProvider,
            //'searchModel' => $searchModel,
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

            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $formModel,
                'description' => "创建角色 (" . $formModel->name . ") 权限为 " . print_r($permissions, true) . " 继承角色 " . print_r($childRoles, true) . "的权限"
            ]));
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

            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'description' => "修改 角色 (" . $name . ") 把权限从" . print_r($oldPermissions, true) . " 修改为 " . print_r($permissions, true) . " 把子角色从 " . print_r($oldRoles, true) . " 修改为 " . print_r($roles, true),
            ]));
            return true;
        }
        return false;
    }

    public function sortRole($name, $sort)
    {
        $role = $this->authManager->getRole($name);
        if( $role === null ) throw new NotFoundHttpException("Not exits role " . $name);
        $formModel = $this->getNewRoleModel();
        $formModel->setAttributes($role);
        $oldSort = $formModel->sort;
        $formModel->sort = $sort;
        $role->data = $formModel->getData();
        $result = $this->authManager->update($name, $role);
        if($result){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'description' => "修改 角色(" . $name . ")排序 " . $oldSort . "为" . $formModel->sort,
            ]));
        }
        return $result;
    }

    public function deleteRole($name)
    {
        $role = $this->authManager->getRole($name);
        if( $role === null ) throw new NotFoundHttpException("Not exits role " . $name);
        $permissions = $this->authManager->getPermissionsByRole($name);
        foreach ($permissions as $permission){
            $result = $this->authManager->remove($permission);
            if( !$result ){
                Yii::error("delete role remove permission " . $permission->name . " error");
            }
        }
        $result = $this->authManager->remove($role);
        if($result){
            Event::trigger(CustomLog::className(), CustomLog::EVENT_CUSTOM, new CustomLog([
                'sender' => $this,
                'description' => "删除 角色(" . $name . ")",
            ]));
        }
        return $result;
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
        $groups = array_keys($permissions);
        $newGroups = [];
        foreach ($groups as $group){
            $newGroups[$group] = $group;
        }
        return $newGroups;
    }

    public function getPermissionCategories()
    {
        $permissions = $this->getPermissionsGroups();
        $categories = [];
        foreach ($permissions as $permission){
            $categories = array_merge($categories, array_keys($permission));
        }
        $newCategories = [];
        foreach ($categories as $category){
            $newCategories[$category] = $category;
        }
        return $newCategories;
    }
}