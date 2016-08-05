<?php

namespace backend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use common\models\Category;

class CategoryController extends BaseController
{

    public function actionIndex()
    {
        $data = Category::getArray();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => -1
            ]
        ]);
        //$dataProvider = new ActiveDataProvider([
        //'query' => $query,
        //]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(){
        $model = new Category();
        if(yii::$app->request->isPost){
            if($model->load(yii::$app->request->post()) && $model->validate() && $model->save()){
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
            'model' => $model
        ]);
    }

    public function getModel($id = '')
    {
        return Category::findOne(['id'=>$id]);
    }

}