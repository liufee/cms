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
use common\services\CategoryServiceInterface;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

/**
 * Category management
 * - data:
 *          table category
 *          column `parent_id` is the parent category id, if equals 0 means first level category
 *
 * Class CategoryController
 * @package backend\controllers
 */
class CategoryController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=内容 category=分类 description-get=列表 sort=310  method=get
     * - item group=内容 category=分类 description-get=查看 sort=311 method=get  
     * - item group=内容 category=分类 description=创建 sort-get=312 sort-post=313 method=get,post  
     * - item group=内容 category=分类 description=修改 sort-get=314 sort-post=315 method=get,post  
     * - item group=内容 category=分类 description-post=删除 sort=316 method=post  
     * - item group=内容 category=分类 description-post=排序 sort=317 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var CategoryServiceInterface $service */
        $service = Yii::$app->get(CategoryServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function() use($service){
                    return [
                        "dataProvider" => $service->getCategoryList(),
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id) use($service){
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
                'data' => function($createResultModel) use($service) {
                    $model = $createResultModel === null ? $service->newModel() : $createResultModel;
                    return [
                        'model' => $model,
                        'categories' => $service->getLevelCategoriesWithPrefixLevelCharacters(),
                    ];
                },
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->update($id, $postData);
                },
                'data' => function($id, $updateResultModel) use($service){
                    $model = $updateResultModel === null ? $service->getDetail($id) : $updateResultModel;
                    return [
                        'model' => $model,
                        'categories' => $service->getLevelCategoriesWithPrefixLevelCharacters(),
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id) use($service){
                    return $service->delete($id);
                }
            ],
            'sort' => [
                'class' => SortAction::className(),
                'sort' => function($id, $sort) use($service){
                    return $service->sort($id, $sort);
                },
            ],
        ];
    }

}