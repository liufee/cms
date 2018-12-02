<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use Yii;
use backend\models\Menu;
use backend\models\search\MenuSearch;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

/**
 * Menu controller
 */
class MenuController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=菜单 category=后台 description-get=列表 sort=210 method=get
     * - item group=菜单 category=后台 description-get=查看 sort=211 method=get  
     * - item group=菜单 category=后台 description=创建 sort-get=212 sort-post=213 method=get,post  
     * - item group=菜单 category=后台 description=修改 sort-get=214 sort-post=215 method=get,post  
     * - item group=菜单 category=后台 description-post=删除 sort=216 method=post  
     * - item group=菜单 category=后台 description-post=排序 sort=217 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    /** @var MenuSearch $searchModel */
                    $searchModel = Yii::createObject([
                        'class' => MenuSearch::className(),
                        'scenario' => 'backend'
                    ]);
                    $dataProvider = $searchModel->search( Yii::$app->getRequest()->getQueryParams() );
                    $data = [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];
                    return $data;
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'backend',
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'backend',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'backend',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Menu::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'backend',
            ],
        ];
    }

}
