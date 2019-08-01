<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */
namespace api\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * Class UserController
 * @package api\controllers
 *
 * 调用/register注册用户后，再次调用/login登录获取accessToken,再次访问/users?access-token=xxxxxxx访问
 */
class UserController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\User";

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
                    'info',//无需access-token的action
                ],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    [
                        'class' => QueryParamAuth::className(),
                        'tokenParam' => 'access-token',
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'info'  => ['GET'],
                ],
            ],
        ]);
    }

    public function actionInfo(){
        return ["我是user无需token可以访问的info"];
    }
}
