<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use Yii;
use backend\actions\ViewAction;
use common\services\MenuServiceInterface;
use common\models\Menu;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\helpers\ArrayHelper;

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
        $service = Yii::$app->get(MenuServiceInterface::MenuService);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(array $query) use($service){
                    $result = $service->getList($query, ['type'=> Menu::TYPE_FRONTEND]);
                    $data = [
                        'dataProvider' => $result['dataProvider'],
                        //'searchModel' => $result['searchModel'],
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
                'class' => CreateAction::className(),
                'create' => function($postData)use($service){
                    return $service->create($postData, ['type'=> Menu::TYPE_FRONTEND]);
                },
                'data' => function($createResultModel) use($service){
                    $model = $createResultModel === null ? $service->getNewModel(['type'=> Menu::TYPE_FRONTEND]) : $createResultModel;
                    return [
                        'model' => $model,
                        'menusNameWithPrefixLevelCharacters' => $service->getMenusNameWithPrefixLevelCharacters(\common\models\Menu::TYPE_BACKEND),
                        'parentMenuDisabledOptions' => [],
                    ];
                },
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData)use($service) {
                    return $service->update($id, $postData);
                },
                'data' => function($id, $updateResultModel)use($service){
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;

                    $parentMenuDisabledOptions = [];
                    $parentMenuDisabledOptions[$id] = ['disabled' => true];//cannot be themselves' sub menu

                    $descendants = $service->getDescendantMenusById($id, \common\models\Menu::TYPE_BACKEND);
                    $descendants = ArrayHelper::getColumn($descendants, 'id');
                    foreach ($descendants as $descendant){//cannot be themselves's sub menu's menu
                        $parentMenuDisabledOptions[$descendant] = ['disabled' => true];
                    }

                    return [
                        'model' => $model,
                        'menusNameWithPrefixLevelCharacters' => $service->getMenusNameWithPrefixLevelCharacters(\common\models\Menu::TYPE_BACKEND),
                        'parentMenuDisabledOptions' => $parentMenuDisabledOptions,
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
                    return $service->sort($id, $sort);
                },
            ],
        ];
    }

}
