<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 11:30
 */

namespace backend\controllers;

use Yii;
use common\services\RBACServiceInterface;
use common\services\RBACService;
use backend\actions\CreateAction;
use backend\actions\DeleteAction;
use backend\actions\IndexAction;
use backend\actions\SortAction;
use backend\actions\UpdateAction;
use backend\actions\ViewAction;

/**
 * RBAC management
 * - data:
 *          table auth_item auth_item_child auth_assignment
 * - description:
 *          backend user RBAC management
 *
 * Class RbacController
 * @package backend\controllers
 */
class RbacController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=权限 category=规则 description-get=列表 sort=500 method=get
     * - item group=权限 category=规则 description-post=排序 sort=501 method=post
     * - item group=权限 category=规则 description=创建 sort-get=502 sort-post=503 method=get,post
     * - item group=权限 category=规则 description=修改 sort-get=504 sort-post=505 method=get,post
     * - item group=权限 category=规则 description-get=查看 sort=506 method=get
     * - item group=权限 category=规则 description-post=删除 sort=507 method=post
     * - item group=权限 category=角色 description-get=列表 sort=510 method=get
     * - item group=权限 category=角色 description-get=查看 sort=515 method=get
     * - item group=权限 category=角色 description=创建 sort-get=511 sort-post=512 method=get,post
     * - item group=权限 category=角色 description=修改 sort-get=513 sort-post=514 method=get,post
     * - item group=权限 category=角色 description-post=排序 sort=516 method=post
     * - item group=权限 category=角色 description-post=删除 sort=517 method=post
     */
    public function actions()
    {
        /** @var RBACServiceInterface $service */
        $service = Yii::$app->get(RBACService::ServiceName);
        return [
            'permissions' => [
                'class' => IndexAction::className(),
                'data' => function($query) use($service){
                    $result = $service->getPermissionList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                }
            ],
            'permission-sort' => [
                'class' => SortAction::className(),
                'sort' => function($name, $sort) use($service){
                    return $service->sortPermission($name, $sort);
                },
            ],
            'permission-create' => [
                "class" => CreateAction::className(),
                'create' => function($postData) use($service){
                    return $service->createPermission($postData);
                },
                'data' => function($createResultModel) use($service){
                    $model = $createResultModel === null ? $service->getNewPermissionModel() : $createResultModel;
                    return [
                        'model' => $model,
                        'groups' => $service->getPermissionGroups(),
                        'categories' => $service->getPermissionCategories(),
                    ];
                },
                'successRedirect' => ['rbac/permissions'],
            ],
            'permission-update' => [
                'primaryKeyIdentity' => 'name',
                "class" => UpdateAction::className(),
                "update" => function($name, $postData) use($service){
                    return $service->updatePermission($name, $postData);
                },
                "data" => function($name, $updateResultModel) use($service){
                    $model = $updateResultModel === null ? $service->getPermissionDetail($name) : $updateResultModel;
                    return [
                        'model' => $model,
                        'groups' => $service->getPermissionGroups(),
                        'categories' => $service->getPermissionCategories(),
                    ];
                }
            ],
            'permission-view-layer' => [
                'primaryKeyIdentity' => 'name',
                'class' => ViewAction::className(),
                'data' => function($name) use($service){
                    return [
                        "model" => $service->getPermissionDetail($name),
                    ];
                },
                'viewFile' => 'permission-view-layer',
            ],
            'permission-delete' => [
                'primaryKeyIdentity' => 'name',
                "class" => DeleteAction::className(),
                "delete" => function($name) use($service) {
                    return $service->deletePermission($name);
                },
            ],
            'roles' => [
                'class' => IndexAction::className(),
                'data' => function($query) use($service){
                    $result = $service->getRoleList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                },
                'viewFile' => 'roles',
            ],
            'role-view-layer' => [
                'class' => ViewAction::className(),
                'primaryKeyIdentity' => 'name',
                'viewFile' => 'role-view-layer',
                'data' => function($name) use($service){
                    return [
                        'model' => $service->getRoleDetail($name),
                    ];
                }
            ],
            'role-create' => [
                "class" => CreateAction::className(),
                'create' => function($postData) use($service){
                    return $service->createRole($postData);
                },
                'data' => function($createResultModel) use($service){
                    $model = $createResultModel === null ? $service->getNewRoleModel() : $createResultModel;
                    return [
                        'model' => $model,
                        'permissions' => $service->getPermissionsGroups(),
                        'roles' => $service->getRoles(),
                    ];
                },
                'successRedirect' => ['rbac/roles']
            ],
            'role-update' => [
                "class" => UpdateAction::className(),
                'primaryKeyIdentity' => 'name',
                "update" => function($name, $postData) use($service){
                    return $service->updateRole($name, $postData);
                },
                'data' => function($name, $updateResultModel) use($service){
                    $model = $updateResultModel === null ? $service->getRoleDetail($name) : $updateResultModel;
                    $roles = $service->getRoles();
                    unset($roles[$name]);
                    return [
                        'model' => $model,
                        'permissions' => $service->getPermissionsGroups(),
                        'roles' => $roles,
                    ];
                },
                'successRedirect' => ['rbac/roles'],
            ],
            'role-sort' => [
                'class' => SortAction::className(),
                'sort' => function($name, $sort) use($service) {
                    return $service->sortRole($name, $sort);
                },
            ],
            'role-delete' => [
                "class" => DeleteAction::className(),
                'primaryKeyIdentity' => 'name',
                "delete" => function($name) use($service) {
                    return $service->deleteRole($name);
                },
            ],
        ];
    }

}