<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use Yii;
use common\models\Article;
use common\services\ArticleServiceInterface;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

class ArticleController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=内容 category=文章 description-get=列表 sort=300 method=get
     * - item group=内容 category=文章 description-get=查看 sort=301 method=get  
     * - item group=内容 category=文章 description=创建 sort-get=302 sort-post=303 method=get,post  
     * - item group=内容 category=文章 description=修改 sort=304 sort-post=305 method=get,post  
     * - item group=内容 category=文章 description-post=删除 sort=306 method=post  
     * - item group=内容 category=文章 description-post=排序 sort=307 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var ArticleServiceInterface $service */
        $service = Yii::$app->get(ArticleServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function($query) use($service){
                    $result = $service->getList($query, ['type'=>Article::ARTICLE]);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
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
                    return $service->create($postData, ['scenario'=>'article']);
                },
                'data' => function($createResultModel,  CreateAction $createAction) use($service){
                    return [
                        'model' => $createResultModel === null ? $service->getNewModel() : $createResultModel,
                    ];
                },
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->update($id, $postData, ['scenario'=>'article']);
                },
                'data' => function($id, $updateResultModel) use($service){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id) : $updateResultModel,
                    ];
                }
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id) use($service){
                    return $service->delete($id);
                },
            ],
            'sort' => [
                'class' => SortAction::className(),
                'sort' => function($id, $sort) use($service){
                    return $service->sort($id, $sort, ['scenario'=>'article']);
                }
            ],
        ];
    }

}