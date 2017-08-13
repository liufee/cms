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
                'class' => IndexAction::class,
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
                'class' => DeleteAction::class,
                'modelClass' => Comment::class,
            ],
            'status' => [
                'class' => StatusAction::class,
                'modelClass' => Comment::class,
            ],
        ];
    }

}