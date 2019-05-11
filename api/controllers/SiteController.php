<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */
namespace api\controllers;

use Yii;
use api\models\form\SignupForm;
use common\models\User;
use api\models\form\LoginForm;
use yii\web\HttpException;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

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

    /**
     * 登录
     *
     * POST /login
     * {"username":"xxx", "password":"xxxxxx"}
     *
     * @return array
     */
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        $loginForm->setAttributes( Yii::$app->getRequest()->post() );
        if ($user = $loginForm->login()) {
            if ($user instanceof IdentityInterface) {
                return [
                    'accessToken' => $user->access_token,
                    'expiredAt' => Yii::$app->params['user.apiTokenExpire'] + time()
                ];
            } else {
                return $user->errors;
            }
        } else {
            return $loginForm->errors;
        }

    }

    /**
     * 注册
     *
     * POST /register
     * {"username":"xxx", "password":"xxxxxxx", "email":"x@x.com"}
     *
     * @return array
     */
    public function actionRegister()
    {
        $signupForm = new SignupForm();
        $signupForm->setAttributes( Yii::$app->getRequest()->post() );
        if( ($user = $signupForm->signup()) instanceof User){
            return [
                "success" => true,
                "username" => $user->username,
                "email" => $user->email
            ];
        }else{
            return [
                "success" => false,
                "error" => $signupForm->getErrors()
            ];
        }
    }

}
