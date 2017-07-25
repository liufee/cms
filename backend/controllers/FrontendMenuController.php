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

/**
 * FrontendMenu controller
 */
class FrontendMenuController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function getIndexData()
    {
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

    /**
     * @inheritdoc
     */
    public function getModel($id = "")
    {
        if ($id == '') {
            $model = new Menu();
        } else {
            $model = Menu::findOne(['id' => $id]);
            if ($model == null) {
                return null;
            }
        }
        $model->setScenario('frontend');
        return $model;
    }

}
