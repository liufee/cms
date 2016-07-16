<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'from' => ['admin@feehi.com'],
                        'to' => ['admin@feehi.com', 'liufee@126.com'],
                        'subject' => '来自 example.com 的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'rbac' => [
            'class' => 'feehi\components\Rbac',
            'superAdministrators' => [
                'admin',
                'administrator',
            ],
            'noNeedAuthentication' => [
                'site/index',
                'site/login',
                'site/logout',
                'site/main',
                'site/error',
                'site/language',
                'admin-user/update-self',
                'error/forbidden',
                'error/not-found',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@feehi/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'menu' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@feehi/messages',
                    'sourceLanguage' => 'zh-CN',
                    'fileMap' => [
                        'app' => 'menu.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'on beforeRequest' => function($event) {
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_INSERT, ['feehi\components\AdminLog', 'create']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE, ['feehi\components\AdminLog', 'update']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_DELETE, ['feehi\components\AdminLog', 'delete']);
        if(isset(\yii::$app->session['language'])) \yii::$app->language = yii::$app->session['language'];
    },
    'on beforeAction' => function($action)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('X-Powered-By', 'feehi');
        if(!yii::$app->user->isGuest){
            if( yii::$app->rbac->checkPermission() === false ){
                //throw new \yii\web\HttpException(403, 'forbidden');
                Yii::$app->response->redirect(['error/forbidden'], 200)->send();
                exit();
            }
        }
        if(yii::$app->user->isGuest && Yii::$app->controller->id.'/'.Yii::$app->controller->action->id != 'site/login') yii::$app->controller->redirect(['site/login']);
    },
    'params' => $params,
];
