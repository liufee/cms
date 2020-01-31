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
                'data' => function()use($service){
                    $result = $service->getList(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $result['dataProvider'],
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
                    return $service->create($postData);
                },
                'data' => function($createResultModel,  CreateAction $createAction)use($service){
                    $model = $createResultModel === null ? $service->getNewModel() : $createResultModel;//if POST create failed, will use the old model with errors
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