<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/1
 * Time: 23:26
 */
namespace backend\controllers;

use yii;
use backend\models\AdminLogSearch;
use backend\models\AdminLog;

class LogController extends BaseController{

    public function actionIndex()
    {
        $searchModel = new AdminLogSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = AdminLog::findOne(['id'=>$id]);
        return $this->render('view', [
           'model' => $model,
        ]);
    }

    public function getModel($id = '')
    {
        return AdminLogSearch::findOne(['id'=>$id]);
    }

}