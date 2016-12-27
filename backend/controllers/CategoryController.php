<?php

namespace backend\controllers;

use yii\data\ArrayDataProvider;
use common\models\Category;

class CategoryController extends BaseController
{

    public function getIndexData()
    {
        $data = Category::getArray();
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

    public function getModel($id = '')
    {
        if($id == ''){
            $model = new Category();
        }else {
            $model = Category::findOne(['id' => $id]);
        }
        return $model;
    }

}