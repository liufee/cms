<?php
/**
 * Created by PhpStorm.
 * User: lf
 * Date: 16/4/2
 * Time: 10:02
 */
namespace backend\controllers;


use yii;
use backend\models\UserSearch;
use frontend\models\User;


class UserController extends BaseController{

    public function getIndexData()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(yii::$app->request->queryParams);
        return [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
    }

    public function getModel($id = '')
    {
        if($id == ''){
            $model = new User(['scenario'=>'create']);
        }else {
            $model = User::findOne(['id' => $id]);
            $model->setScenario('update');
        }
        return $model;
    }

}