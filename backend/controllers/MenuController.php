<?php
namespace backend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use backend\models\Menu;

/**
 * Menu controller
 */
class MenuController extends BaseController
{

    public function actionIndex()
    {
        if(yii::$app->request->getIsPost()){
            $model = new Menu();
            $model->saveSort(yii::$app->request->post());
        }
        $data = Menu::getMenuArray();//var_dump($data);die;
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
        $model = new Menu(['scenario'=>'backend']);
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
        $model->setScenario('backend');
        return $model;
    }

}
