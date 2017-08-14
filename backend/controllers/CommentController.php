<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-03 22:03
 */

namespace backend\controllers;

use yii;
use backend\models\Comment;
use backend\models\CommentSearch;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\StatusAction;

class CommentController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $searchModel = new CommentSearch();
                    $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Comment::className(),
            ],
            'status' => [
                'class' => StatusAction::className(),
                'modelClass' => Comment::className(),
            ],
        ];
    }

}