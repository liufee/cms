<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-09-12 11:30
 */

namespace backend\controllers;

use Yii;
use backend\actions\CreateAction;
use backend\actions\DeleteAction;
use backend\actions\IndexAction;
use backend\actions\SortAction;
use backend\actions\UpdateAction;
use backend\actions\ViewAction;
use backend\models\search\RbacFormSearch;
use backend\models\form\RbacForm;

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
        return [
            'permissions' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var RbacFormSearch $searchModel */
                    $searchModel = Yii::createObject(['class' => RbacFormSearch::className(),'scenario'=>'permission']);
                    $dataProvider = $searchModel->searchPermissions(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'permission-sort' => [
                'class' => SortAction::className(),
                'model' => function($where){
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'permission']);
                    $model->fillModel($where["name"]);
                    return $model;
                },
                'executeMethod' => function($model){
                    /** @var RbacForm $model */
                    return $model->updatePermission($model->name);
                }
            ],
            'permission-create' => [
                "class" => CreateAction::className(),
                'model' => function(){
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'permission']);
                    return $model;
                },
                'executeMethod' => function($model){
                    /** @var RbacForm $model */
                    if( Yii::$app->getRequest()->post() && $model->validate() && $model->createPermission() ){
                        return true;
                    }else{
                        return false;
                    }
                },
                'successRedirect' => ['rbac/permissions']
            ],
            'permission-update' => [
                "class" => UpdateAction::className(),
                "model" => function(){
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'permission']);
                    $name = Yii::$app->getRequest()->get("name", "");
                    $model->fillModel($name);
                    return $model;
                },
                "executeMethod" => function($model){
                    /** @var RbacForm $model */
                    if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->updatePermission($model->name)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            ],
            'permission-view-layer' => [
                'class' => ViewAction::className(),
                'model' => function(){
                    $name = Yii::$app->getRequest()->get("name", "");
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'permission']);
                    $model->fillModel($name);
                    return $model;
                },
                'viewFile' => 'permission-view-layer',
            ],
            'permission-delete' => [
                "class" => DeleteAction::className(),
                'paramSign' => 'name',
                "model" => function($name){
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'permission']);
                    $model->fillModel($name);
                    return $model;
                },
                "executeMethod" => function($model){
                    /** @var RbacForm $model */
                    if ( $model->deletePermission() ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            ],
            'roles' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var RbacFormSearch $searchModel */
                    $searchModel = Yii::createObject(['class' => RbacFormSearch::className(), 'scenario'=>'role']);
                    $dataProvider = $searchModel->searchRoles( Yii::$app->getRequest()->getQueryParams() );
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'role-view-layer' => [
                'class' => ViewAction::className(),
                'model' => function(){
                    $name = Yii::$app->getRequest()->get("name", "");
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'role']);
                    $model->fillModel($name);
                    return $model;
                }
            ],
            'role-create' => [
                "class" => CreateAction::className(),
                'model' => function(){
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'role']);
                    return $model;
                },
                'executeMethod' => function($model){
                    /** @var RbacForm $model */
                    if( Yii::$app->getRequest()->post() && $model->validate() && $model->createRole() ){
                        return true;
                    }else{
                        return false;
                    }
                },
                'successRedirect' => ['rbac/roles']
            ],
            'role-update' => [
                "class" => UpdateAction::className(),
                "model" => function(){
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'role']);
                    $name = Yii::$app->getRequest()->get("name", "");
                    $model->fillModel($name);
                    return $model;
                },
                "executeMethod" => function($model){
                    $name = Yii::$app->getRequest()->get("name", "");
                    /** @var RbacForm $model */
                    if ($model->load(Yii::$app->getRequest()->post()) && $model->validate() && $model->updateRole($name)) {
                        return true;
                    } else {
                        return false;
                    }
                },
            ],
            'role-sort' => [
                'class' => SortAction::className(),
                'model' => function($where){
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'role']);
                    $model->fillModel($where["name"]);
                    return $model;
                },
                'executeMethod' => function($model){
                    /** @var RbacForm $model */
                    return $model->updateRole($model->name);
                }
            ],
            'role-delete' => [
                "class" => DeleteAction::className(),
                'paramSign' => 'name',
                "model" => function($name){
                    /** @var RbacForm $model */
                    $model = Yii::createObject(['class' => RbacForm::className(), 'scenario'=>'role']);
                    $model->fillModel($name);
                    return $model;
                },
                "executeMethod" => function($model){
                    /** @var RbacForm $model */
                    if ( $model->deleteRole() ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            ],
        ];
    }

}