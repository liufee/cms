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

class ArticleController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=内容 category=文章 description-get=列表 sort=300 method=get
     * - item group=内容 category=文章 description-get=查看 sort=301 method=get  
     * - item group=内容 category=文章 description=创建 sort-get=302 sort-post=303 method=get,post  
     * - item group=内容 category=文章 description=修改 sort=304 sort-post=305 method=get,post  
     * - item group=内容 category=文章 description-post=删除 sort=306 method=post  
     * - item group=内容 category=文章 description-post=排序 sort=307 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var $searchModel ArticleSearch */
                    $searchModel = Yii::createObject( ArticleSearch::className() );
                    $dataProvider = $searchModel->search( Yii::$app->getRequest()->getQueryParams() );
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
                'scenario' => 'article',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Article::className(),
                'scenario' => 'article',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Article::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Article::className(),
                'scenario' => 'article',
            ],
        ];
    }

}