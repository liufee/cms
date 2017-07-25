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
    'language' => 'zh-CN',//默认语言
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => backend\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_backend_identity'],
            'idParam' => '__backend__id',
            'returnUrlParam' => '__backend__returnUrl',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => yii\log\EmailTarget::class,
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'to' => ['admin@feehi.com', 'liufee@126.com'],//当触发levels配置的错误级别时，发送到此些邮箱（请改成自己的邮箱）
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
            'superAdministrators' => [//超级管理员用户，不受权限管理的控制
                'admin',
                'administrator',
            ],
            'noNeedAuthentication' => [//无需权限管理的控制器/操作，任意角色、用户，包括未登录均可访问
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
                'assets/ueditor'
            ],
        ],
        'i18n' => [
            'translations' => [//多语言包设置
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'menu' => [
                    'class' => yii\i18n\PhpMessageSource::class,
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
    'on beforeRequest' => [feehi\components\Feehi::class, 'backendInit'],
    'on beforeAction' => [backend\components\Rbac::class, 'checkPermission'],
    'params' => $params,
];
