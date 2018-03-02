<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use yii\data\ArrayDataProvider;
use common\models\Category;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

class CategoryController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $data = Category::getCategories();
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
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Category::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Category::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Category::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Category::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Category::className(),
            ],
        ];
    }

}