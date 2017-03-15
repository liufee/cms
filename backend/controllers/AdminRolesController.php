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

class AdminRolesController extends BaseController
{
    public function getIndexData()
    {
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

    public function getModel($id = '')
    {
        if ($id == '') {
            $model = new AdminRoles();
        } else {
            $model = AdminRoles::findOne(['id' => $id]);
        }
        return $model;
    }

    public function actionAssign($id = '')
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $role_id = yii::$app->getRequest()->get('id');
            $ids = explode(',', yii::$app->getRequest()->post('ids', ''));
            $model = new AdminRolePermission();
            $model->assignPermission($role_id, $ids);
            Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            return $this->redirect(['assign', 'id' => yii::$app->request->get('id', '')]);
        }
        $model = AdminRolePermission::findAll(['role_id' => $id]);
        $treeJson = Menu::getBackendMenuJson();
        return $this->render('assign', [
            'model' => $model,
            'treeJson' => $treeJson,
            'role_name' => AdminRoles::findOne(['id' => $id])['role_name'],
        ]);
    }
}