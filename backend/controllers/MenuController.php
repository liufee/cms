<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use yii;
use backend\models\Menu;
use backend\models\MenuSearch;

/**
 * Menu controller
 */
class MenuController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch(['scenario' => 'backend']);
        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
        $data = [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
        return $this->render('index', $data);
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
        $model->setScenario('backend');
        return $model;
    }

}
