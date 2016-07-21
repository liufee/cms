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


class AdminRolesController extends BaseController
{
    public function actionIndex()
    {
        $query = AdminRoles::find();
        $dataProvider = new yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC,
                ]
            ]
        ]);
        return $this->render('index', [
           'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new AdminRoles();
        if( yii::$app->request->isPost ){
            if( $model->load(yii::$app->request->post()) && $model->save() ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('Error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        return AdminRoles::findOne(['id'=>$id]);
    }

    public function actionAssign($id = '')
    {
        $menus = Menu::getMenuArray(Menu::BACKEND_TYPE);//var_dump($menus);die;
        $model = new AdminRolePermission();
        if(yii::$app->request->isPost){
            if( $model->assignPermission(yii::$app->request->post("permission")) ){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['update', 'id'=>$model->primaryKey]);
            }else{
                Yii::$app->getSession()->setFlash('Error', yii::t('app', 'Error'));
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('reason', $err);
            }
        }
        $model =  AdminRolePermission::findAll(['role_id'=>$id]);//var_dump($model);die;
        return $this->render('assign', [
            'menus' => $menus,
            'model' => $model,
        ]);
    }
}