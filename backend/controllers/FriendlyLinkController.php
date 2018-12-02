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
use backend\models\search\FriendlyLinkSearch;
use backend\models\FriendlyLink;
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
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var $searchModel FriendlyLinkSearch */
                    $searchModel = Yii::createObject( FriendlyLinkSearch::className() );
                    $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => FriendlyLink::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => FriendlyLink::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => FriendlyLink::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => FriendlyLink::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => FriendlyLink::className(),
            ],
        ];
    }

}
