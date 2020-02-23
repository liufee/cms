<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-05 12:47
 */

namespace backend\controllers;

use Yii;
use backend\actions\ViewAction;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use common\services\AdServiceInterface;

/**
 * Advertisement management
 * - data:
 *          table options with column `type` equal \common\models\Options::TYPE_AD
 *          column `value` is a json format, like {"ad":"x.png"}
 *
 * Class AdController
 * @package backend\controllers
 */
class AdController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=运营管理 category=广告 description-get=列表 sort=620 method=get
     * - item group=运营管理 category=广告 description-get=查看 sort=621 method=get  
     * - item group=运营管理 category=广告 description=创建 sort-get=622 sort-post=623 method=get,post  
     * - item group=运营管理 category=广告 description=修改 sort-get=624 sort-post=625 method=get,post  
     * - item group=运营管理 category=广告 description-post=删除 sort=626 method=post  
     * - item group=运营管理 category=广告 description-post=排序 sort=627 method=post  
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var AdServiceInterface $service */
        $service = Yii::$app->get(AdServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query)use($service){
                    /** @var array $query $_GET query params */
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
            'create' => [
                'class' => CreateAction::className(),
                'create' => function($postData) use($service){
                    /** @var $postData $_POST data */
                    return $service->create($postData);
                },
                'data' => function($createResultModel,  CreateAction $createAction)use($service){
                    /**
                     * same path(`/path/create`) have two HTTP method
                     *  - GET for display create page
                     *  - POST do a create operation(write data to database), then redirect to index or show a create error
                     *
                     * if $createResultModel equals null means that is a GET request, need to show create page,
                     * otherwise means POST request, $createResultModel be the model of created(maybe contains data validation error)
                     */
                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData, UpdateAction $updateAction) use($service){
                    return $service->update($id, $postData);
                },
                'data' => function($id, $updateResultModel) use($service){
                    /**
                     * same path(`/path/update`) have two HTTP method
                     *  - GET for display update page
                     *  - POST do a update operation(write data to database), then redirect to index or show a update error
                     *
                     * if $updateResultModel equals null means that is a GET request, need to show update page,
                     * otherwise means POST request, $updateResultModel be the model of updated(maybe contains data validation error)
                     */
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;
                    return [
                        'model' => $model,
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id)use($service){
                    return $service->delete($id);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'sort' => function($id, $sort)use($service){
                    return $service->sort($id, $sort);
                },
            ],
        ];
    }
}