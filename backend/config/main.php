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
            'class' => 'backend\components\Rbac',
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
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'menu' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'zh-CN',
                    'fileMap' => [
                        'app' => 'menu.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'on beforeRequest' => ['feehi\components\Feehi', 'backendInit'],
    'on beforeAction' => ['backend\components\Rbac', 'checkPermission'],
    'params' => $params,
];
