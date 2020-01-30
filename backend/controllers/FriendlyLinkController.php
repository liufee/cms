<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use Yii;
use common\services\FriendlyLinkServiceInterface;
use backend\actions\ViewAction;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

/**
 * FriendLink controller
 */
class FriendlyLinkController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=其他 category=友情链接 description-get=列表 sort=700 method=get
     * - item group=其他 category=友情链接 description-get=查看 sort=701 method=get  
     * - item group=其他 category=友情链接 description=创建 sort-get=702 sort-post=703 method=get,post  
     * - item group=其他 category=友情链接 description=修改 sort-get=704 sort-post=705 method=get,post  
     * - item group=其他 category=友情链接 description-post=删除 sort=706 method=post  
     * - item group=其他 category=友情链接 description-post=排序 sort=707 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var FriendlyLinkServiceInterface $service */
        $service =  Yii::$app->get("friendlyService");
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
            'create' => [
                'class' => CreateAction::className(),
                'data' => function()use($service){
                    return[
                        'model' => $service->getNewModel(),
                    ];
                },
                'create' => function(array $postData) use($service){
                    return $service->create($postData);
                },
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
                'update' => function($id, array $postData) use($service){
                    return $service->update($id, $postData);
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
                    $service->sort($id, $sort);
                },
            ],
        ];
    }
}
