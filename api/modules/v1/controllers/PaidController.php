<?php

namespace api\modules\v1\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class PaidController extends \yii\rest\Controller
{
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
                    'info' => ['GET'],
                ],
            ],
        ]);
    }

    /**
     * 访问路由 /v1/paids 或/v1/paid/index (p.s如果入口在frontend/web/api/index.php则还需在前加上api)
     *
     * @return array
     */
    public function actionIndex()
    {
        return ["我是v1 paid/index 需要access-token才能访问的接口"];
    }

    /**
     * 访问路由 /v1/paid/info (p.s如果入口在frontend/web/api/index.php则还需在前加上api)
     *
     * @return array
     */
    public function actionInfo()
    {
        return ["我是v1 paid/info 我不需要access-token也能访问"];
    }

}
