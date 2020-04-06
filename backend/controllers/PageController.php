<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use Yii;
use common\services\ArticleServiceInterface;
use common\models\Article;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

/**
 * Page management
 * - data:
 *          table article article_content
 * - description:
 *          frontend single management. please find single page by column `sub_title`
 *
 * Class PageController
 * @package backend\controllers
 */
class PageController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=内容 category=单页 description-get=列表 sort=330 method=get
     * - item group=内容 category=单页 description-get=查看 sort=331 method=get  
     * - item group=内容 category=单页 description=创建 sort-get=332 sort-post=333 method=get,post  
     * - item group=内容 category=单页 description=修改 sort-get=334 sort-post=335 method=get,post  
     * - item group=内容 category=单页 description-post=删除 sort=336 method=post  
     * - item group=内容 category=单页 description-post=排序 sort=337 method=post
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
                    $result = $service->getList($query, ['type'=> Article::SINGLE_PAGE]);
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
                        'model' => $service->getDetail($id, ['scenario'=>ArticleServiceInterface::ScenarioPage]),
                    ];
                },
            ],
            'create' => [
                'class' => CreateAction::className(),
                'create' => function($postData) use($service){
                    return $service->create($postData, ['scenario'=>ArticleServiceInterface::ScenarioPage]);
                },
                'data' => function($createResultModel) use($service){
                    return [
                        'model' => $createResultModel === null ? $service->newModel(['scenario'=>ArticleServiceInterface::ScenarioPage]) : $createResultModel['articleModel'],
                        'contentModel' => $createResultModel === null ? $service->newArticleContentModel() : $createResultModel['articleContentModel'] ,
                    ];
                },
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'update' => function($id, $postData) use($service){
                    return $service->update($id, $postData, ['scenario'=>ArticleServiceInterface::ScenarioPage]);
                },
                'data' => function($id, $updateResultModel) use($service){
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id, ['scenario'=>ArticleServiceInterface::ScenarioPage]) : $updateResultModel['articleModel'],
                        'contentModel' => $updateResultModel === null ? $service->getArticleContentDetail($id) : $updateResultModel['articleContentModel'],
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
                    return $service->sort($id, $sort, ['scenario'=>ArticleServiceInterface::ScenarioPage]);
                }
            ],
        ];
    }

}