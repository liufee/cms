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
            'identityCookie' => ['name' => '_backend_identity'],
            'idParam' => '__backend__id',
            'returnUrlParam' => '__backend__returnUrl',
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
                        'to' => ['admin@feehi.com', 'liufee@126.com'],
                        'subject' => '来自 Feehi CMS 的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
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
                'site/captcha',
                'site/error',
                'site/language',
                'admin-user/update-self',
                'error/forbidden',
                'error/not-found',
                'debug/default/toolbar',
                'debug/default/view',
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
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_FIND, function($event){
            if( isset($event->sender->updated_at) && $event->sender->updated_at == 0 ) $event->sender->updated_at = null;
        });
        \feehi\components\Feehi::setBackendConfig();
        if(isset(\yii::$app->session['language'])) \yii::$app->language = yii::$app->session['language'];
        if( yii::$app->getRequest()->getIsAjax() ){
            yii::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;
        }else{
            yii::$app->getResponse()->format = \yii\web\Response::FORMAT_HTML;
        }
    },
    'on beforeAction' => function($action)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('X-Powered-By', 'feehi');
        if(!yii::$app->user->isGuest){
            if( yii::$app->rbac->checkPermission() === false ){
                //throw new \yii\web\HttpException(403, 'forbidden');
                if( yii::$app->getRequest()->getIsAjax() ){
                    yii::$app->getResponse()->content = json_encode( ['code'=>1001, 'message'=>'权限不允许'] );
                    yii::$app->getResponse()->send();
                }else {
                    Yii::$app->response->redirect(['error/forbidden'], 302)->send();
                }
                exit();
            }
        }
        if(yii::$app->user->isGuest &&
            !in_array(Yii::$app->controller->id.'/'.Yii::$app->controller->action->id, ['site/login', 'user/request-password-reset', 'user/reset-password', 'site/captcha']) &&
            !in_array(Yii::$app->controller->module->id, ['debug'])
        ) yii::$app->controller->redirect(['site/login']);
    },
    'params' => $params,
];
