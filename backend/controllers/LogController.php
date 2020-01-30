<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-01 23:26
 */

namespace backend\controllers;

use common\services\LogServiceInterface;
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
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var LogServiceInterface $service */
        $service = Yii::$app->get("logService");
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(array $query)use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id)use($service){
                    return $service->delete($id);
                }
            ],
        ];
    }

}