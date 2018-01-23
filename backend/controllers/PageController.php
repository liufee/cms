<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use yii;
use backend\models\Article;
use backend\models\search\ArticleSearch;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

class PageController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $searchModel = new ArticleSearch();
                    $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams(), Article::SINGLE_PAGE);
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Article::className(),
                'scenario' => 'page',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Article::className(),
                'scenario' => 'page',
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Article::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Article::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Article::className(),
                'scenario' => 'page',
            ],
        ];
    }

}