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
use common\models\Category;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

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
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    $data = Category::getCategories();
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