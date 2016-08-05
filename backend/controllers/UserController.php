<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 10:02
 */
namespace backend\controllers;


use yii;
use backend\models\UserSearch;
use frontend\models\User;


class UserController extends BaseController{

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate()
    {
        $model = new User(['scenario'=>'create']);
        if (yii::$app->request->isPost) {
            if($model->load(Yii::$app->request->post()) && $model->save()){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            }else{
                $errors = $model->getErrors();
                $err = '';
                foreach($errors as $v){
                    $err .= $v[0].'<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
            }
        }
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        $model = User::findOne(['id'=>$id]);
        $model->setScenario('update');
        return $model;
    }

}