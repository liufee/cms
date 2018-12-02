<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-03 22:03
 */

namespace backend\controllers;

use Yii;
use backend\actions\ViewAction;
use backend\actions\UpdateAction;
use backend\models\Comment;
use backend\models\search\CommentSearch;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

class CommentController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=内容 category=评论 description-get=列表 sort=320 method=get
     * - item group=内容 category=评论 description-get=查看 sort=321 method=get  
     * - item group=内容 category=评论 description=修改 sort-get=322 sort-post=323 method=get,post 
     * - item group=内容 category=评论 description-post=删除 sort=324 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var CommentSearch $searchModel */
                    $searchModel = Yii::createObject( CommentSearch::className() );
                    $dataProvider = $searchModel->search( Yii::$app->getRequest()->getQueryParams() );
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Comment::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Comment::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Comment::className(),
            ],
        ];
    }

}