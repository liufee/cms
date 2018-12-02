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
use yii\data\ArrayDataProvider;
use frontend\models\Menu;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

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
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $data = Menu::getMenus(Menu::FRONTEND_TYPE);
                    $dataProvider = Yii::createObject([
                        'class' => ArrayDataProvider::className(),
                        'allModels' => $data,
                        'pagination' => [
                            'pageSize' => -1
                        ]
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'frontend',
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'frontend',
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'frontend',
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Menu::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Menu::className(),
                'scenario' => 'frontend',
            ],
        ];
    }

}
