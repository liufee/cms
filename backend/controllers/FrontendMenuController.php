<?php
namespace backend\controllers;

use yii\data\ArrayDataProvider;
use common\models\Menu;

/**
 * FrontendMenu controller
 */
class FrontendMenuController extends BaseController
{


    public function getIndexData()
    {
        $data = Menu::getMenuArray(Menu::FRONTEND_TYPE);//var_dump($data);die;
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

    public function getModel($id="")
    {
        if($id == ''){
            $model = new Menu();
        }else {
            $model = Menu::findOne(['id' => $id]);
            if ($model == null) return null;
        }
        $model->setScenario('frontend');
        return $model;
    }

}
