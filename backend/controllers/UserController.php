<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 10:02
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use Yii;
use frontend\models\User;
use frontend\models\search\UserSearch;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

class UserController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=用户 category=前台用户 description-get=列表 sort=400 method=get
     * - item group=用户 category=前台用户 description-get=查看 sort=401 method=get  
     * - item group=用户 category=前台用户 description=创建 sort-get=402 sort-post=403 method=get,post  
     * - item group=用户 category=前台用户 description=修改 sort-get=404 sort-post=405 method=get,post  
     * - item group=用户 category=前台用户 description-post=删除 sort=406 method=post  
     * - item group=用户 category=前台用户 description-post=排序 sort=407 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var UserSearch $searchModel */
                    $searchModel = Yii::createObject(UserSearch::className());
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => User::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => User::className(),
                'scenario' => 'create',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => User::className(),
                'scenario' => 'update',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => User::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => User::className(),
            ],
        ];
    }
}