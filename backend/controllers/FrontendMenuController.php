<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use common\services\MenuServiceInterface;
use Yii;
use yii\data\ArrayDataProvider;
use frontend\models\Menu;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\db\ActiveRecord;

/**
 * FrontendMenu controller
 */
class FrontendMenuController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=菜单 category=前台 description-get=列表 sort=200 method=get
     * - item group=菜单 category=前台 description-get=查看 sort=201 method=get  
     * - item group=菜单 category=前台 description=创建 sort-get=202 sort-post=203 method=get,post  
     * - item group=菜单 category=前台 description=修改 sort-get=204 sort-post=205 method=get,post  
     * - item group=菜单 category=前台 description-post=删除 sort=206 method=post  
     * - item group=菜单 category=前台 description-post=排序 sort=207 method=post  
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var MenuServiceInterface $service */
        $service = Yii::$app->get("menuService");
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(array $query) use($service){
                    $result = $service->getList($query, ['type'=> \backend\models\Menu::TYPE_FRONTEND]);
                    $data = [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                    return $data;
                },
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model'=>$service->getDetail($id)
                    ];
                },
            ],
            'create' => [
                'data' => function() use($service){
                    /** @var ActiveRecord $model */
                    $model = $service->getNewModel();
                    $model->loadDefaultValues();
                    return [
                        'model'=>$model,
                    ];
                },
                'class' => CreateAction::className(),
                'create' => function($postData)use($service){
                    return $service->create($postData, ['type'=> \backend\models\Menu::TYPE_FRONTEND]);
                }
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData)use($service) {
                    return $service->update($id, $postData);
                },
                'data' => function($id)use($service){
                    return [
                        'model'=>$service->getDetail($id)
                    ];
                },
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
