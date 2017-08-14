<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 21:54
 */

namespace backend\controllers;

use yii;
use backend\models\AdminRolePermission;
use backend\models\Menu;
use backend\models\AdminRoles;
use yii\data\ActiveDataProvider;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

class AdminRolesController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $query = AdminRoles::find();
                    $dataProvider = new ActiveDataProvider([
                        'query' => $query,
                        'sort' => [
                            'defaultOrder' => [
                                'created_at' => SORT_ASC,
                            ]
                        ]
                    ]);
                    return [
                        'dataProvider' => $dataProvider
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => AdminRoles::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => AdminRoles::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => AdminRoles::className(),
            ],
        ];
    }

    /**
     * 给角色赋予权限
     *
     * @param string $id
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAssign($id = '')
    {
        $roleName = AdminRoles::findOne(['id' => $id])['role_name'];
        if( $roleName === null ) {
            Yii::$app->getSession()->setFlash('error', yii::t('app', 'Role does not exists'));
            return $this->redirect(['index']);
        }
        if (yii::$app->getRequest()->getIsPost()) {
            $role_id = yii::$app->getRequest()->get('id');
            if( $role_id != 1 ) {//超级管理员无需写入权限信息
                $temp = yii::$app->getRequest()->post('ids', '');
                $ids = [];
                if (! empty($temp)) {
                    $ids = explode(',', $temp);
                }
                $model = new AdminRolePermission();
                $model->assignPermission($role_id, $ids);
            }
            Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            return $this->redirect(['assign', 'id' => yii::$app->request->get('id', '')]);
        }
        $model = AdminRolePermission::findAll(['role_id' => $id]);
        $treeJson = Menu::getBackendMenuJson();
        if( $id == 1 ){//超级管理员拥有所有权限，且不能被修改
            $treeJson = json_decode($treeJson, true);
            foreach ($treeJson as &$v){
                $v['state'] = [
                    'selected' => true,
                    'disabled' => true,
                ];
                if( isset($v['children']) ){
                    $v['children'] = self::_defaultSuperAdministrator($v['children']);
                }
            }
            $treeJson = json_encode($treeJson);
        }
        return $this->render('assign', [
            'model' => $model,
            'treeJson' => $treeJson,
            'role_name' => $roleName,
        ]);
    }

    /**
     * 默认超管的所有权限选中
     *
     * @param $children
     * @return mixed
     */
    private static function _defaultSuperAdministrator($children)
    {
        foreach ($children as &$v) {
            $v['state'] = ['selected' => true, 'disabled' => true];
            if( isset($v['state']) ) $v['children'] = self::_defaultSuperAdministrator($v['children']);
        }
        return $children;
    }

}