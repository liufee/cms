<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/11
 * Time: 21:54
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
        if($id == '') {
            $model = new AdminRoles();
        }else{
            $model = AdminRoles::findOne(['id' => $id]);
        }
        return $model;
    }

    public function actionAssign($id = '')
    {
        if(yii::$app->request->isPost){
            $data = explode(',', yii::$app->request->post('ids', ''));
            $model = new AdminRolePermission();
            $model->assignPermission($data);
            Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            return $this->redirect(['assign', 'id'=>yii::$app->request->get('id', '')]);
        }
        $model =  AdminRolePermission::findAll(['role_id'=>$id]);
        $treeJson = Menu::getBackendMenuJson();
        return $this->render('assign', [
            'model' => $model,
            'treeJson' => $treeJson,
            'role_name' => AdminRoles::findOne(['id'=>$id])['role_name'],
        ]);
    }
}