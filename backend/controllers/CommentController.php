<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/11
 * Time: 下午10:03
 */
namespace backend\controllers;

use yii;
use backend\models\CommentSearch;

class CommentController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function getModel($id='')
    {
        return CommentSearch::findOne(['id'=>$id]);
    }
}