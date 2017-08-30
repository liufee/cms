<?php
namespace api\controllers;

use yii\web\Response;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\User";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }
}
