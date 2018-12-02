<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-01 23:26
 */

namespace backend\controllers;

use Yii;
use backend\models\search\AdminLogSearch;
use backend\models\AdminLog;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;

class LogController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=其他 category=日志 description-get=列表 sort=711 method=get
     * - item group=其他 category=日志 description-get=查看 sort=712 method=get  
     * - item group=其他 category=日志 description-post=删除 sort=723 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var AdminLogSearch $searchModel */
                    $searchModel = Yii::createObject( AdminLogSearch::className() );
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => AdminLog::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => AdminLog::className(),
            ],
        ];
    }

}