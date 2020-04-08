<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-01 23:26
 */

namespace backend\controllers;

use Yii;
use common\services\LogServiceInterface;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;

/**
 * Admin operation log management
 * - data:
 *          table admin_log
 *          when backend admin create/update/delete database table record, will be generate a operation log
 *
 * Class AdController
 * @package backend\controllers
 */
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
        $service = Yii::$app->get(LogServiceInterface::ServiceName);
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
                'doDelete' => function($id)use($service){
                    return $service->delete($id);
                }
            ],
        ];
    }

}