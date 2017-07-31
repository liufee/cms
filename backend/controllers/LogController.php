<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-01 23:26
 */

namespace backend\controllers;

use yii;
use backend\models\AdminLogSearch;
use backend\models\AdminLog;

class LogController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        $searchModel = new AdminLogSearch();
        $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 日志详情
     *
     * @param $id
     * @return string
     */
    public function actionViewLayer($id)
    {
        $model = AdminLog::findOne(['id' => $id]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getModel($id = '')
    {
        return AdminLogSearch::findOne(['id' => $id]);
    }

}