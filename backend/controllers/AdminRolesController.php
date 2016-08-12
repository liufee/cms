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
        $menus = Menu::getMenuArray(Menu::BACKEND_TYPE);
        $model = new AdminRolePermission();
        if(yii::$app->request->isPost){
            if( $model->assignPermission(yii::$app->request->post("permission")) ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model =  AdminRolePermission::findAll(['role_id'=>$id]);
        return $this->render('assign', [
            'menus' => $menus,
            'model' => $model,
        ]);
    }
}