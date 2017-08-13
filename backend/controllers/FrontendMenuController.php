<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */
namespace backend\controllers;

use yii\data\ArrayDataProvider;
use common\models\Menu;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\StatusAction;

/**
 * FrontendMenu controller
 */
class FrontendMenuController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
                'data' => function(){
                    $data = Menu::getMenus(Menu::FRONTEND_TYPE);
                    $dataProvider = new ArrayDataProvider([
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
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => Menu::class,
                'scenario' => 'frontend',
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => Menu::class,
                'scenario' => 'frontend',
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => Menu::class,
            ],
            'sort' => [
                'class' => SortAction::class,
                'modelClass' => Menu::class,
            ],
            'status' => [
                'class' => StatusAction::class,
                'modelClass' => Menu::class,
            ],
        ];
    }

}
