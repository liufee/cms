<?php
namespace api\controllers;

use yii\web\Response;

class SiteController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\Article";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'login' => ['POST'],
            'register' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        return [
            "feehi api service"
        ];
    }

    public function actionLogin()
    {
        return [
            "username" => 'test',
            "sex" => "male",
        ];
    }

    public function actionRegister()
    {
        return [
            "success" => true
        ];
    }

}
