<?php
namespace backend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use common\models\Menu;

/**
 * FrontendMenu controller
 */
class FrontendMenuController extends BaseController
{


    public function actionIndex()
    {
        $data = \backend\models\Menu::getMenuArray(Menu::FRONTEND_TYPE);//var_dump($data);die;
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

    public function actionCreate()
    {
        $model = new Menu(['scenario'=>'frontend']);
        $model->type = Menu::FRONTEND_TYPE;
        if (yii::$app->request->isPost) {
            if($model->load(Yii::$app->request->post()) && $model->save()){
                Yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('error', yii::t('app', 'Error'));
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

    public function getModel($id="")
    {
        $model = Menu::findOne(['id'=>$id]);
        $model->setScenario('frontend');
        return $model;
    }

}
