<?php
namespace backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\FriendLink;

/**
 * FriendLink controller
 */
class FriendLinkController extends BaseController
{


    public function actionIndex()
    {
        $query = FriendLink::find();//var_dump($data);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_ASC,
                ],
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new FriendLink();
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

    public function getModel($id="")
    {
        return FriendLink::findOne(['id'=>$id]);
    }

}
