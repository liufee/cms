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
        $data = Menu::getMenuArray(Menu::FRONTEND_TYPE);//var_dump($data);die;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => -1
            ]
        ]);
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

    public function actionDelete($id)
    {
        if(yii::$app->request->getIsAjax()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        $children = Menu::getDescendants($id, Menu::FRONTEND_TYPE);
        if(!empty($children)) throw new \yii\web\ForbiddenHttpException(yii::t('app', 'Sub Menu exists, cannot be deleted'));
        return parent::actionDelete($id);
    }

    public function getModel($id="")
    {
        $model = Menu::findOne(['id'=>$id]);
        if($model == null) return null;
        $model->setScenario('frontend');
        return $model;
    }

}
