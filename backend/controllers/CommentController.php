<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-10-03 22:03
 */

namespace backend\controllers;

use Yii;
use common\services\CommentServiceInterface;
use backend\actions\ViewAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;

class CommentController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=内容 category=评论 description-get=列表 sort=320 method=get
     * - item group=内容 category=评论 description-get=查看 sort=321 method=get  
     * - item group=内容 category=评论 description=修改 sort-get=322 sort-post=323 method=get,post 
     * - item group=内容 category=评论 description-post=删除 sort=324 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var CommentServiceInterface $service */
        $service = Yii::$app->get("commentService");
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
            'update' => [
                'class' => UpdateAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
                'update' => function($id, array $postData)use($service){
                    return $service->update($id, $postData);
                },
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'delete' => function($id)use($service){
                    return $service->delete($id);
                },
            ],
        ];
    }

}