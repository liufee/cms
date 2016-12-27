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
use yii\web\NotFoundHttpException;

class CommentController extends BaseController
{
    public function getIndexData()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function actionCreate()
    {
        throw new NotFoundHttpException();
    }

    public function actionUpdate($id='')
    {
        throw new NotFoundHttpException();
    }

    public function getModel($id='')
    {
        return CommentSearch::findOne(['id'=>$id]);
    }
}