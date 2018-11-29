<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use Yii;
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

    /**
     * @auth
     * - item group=内容 category=单页 description-get=列表 method=get
     * - item group=内容 category=单页 description-get=查看 method=get  
     * - item group=内容 category=单页 description=创建 method=get,post  
     * - item group=内容 category=单页 description=修改 method=get,post  
     * - item group=内容 category=单页 description-post=删除 method=post  
     * - item group=内容 category=单页 description-post=排序 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var ArticleSearch $searchModel */
                    $searchModel = Yii::createObject( ArticleSearch::className() );
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams(), Article::SINGLE_PAGE);
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Article::className(),
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