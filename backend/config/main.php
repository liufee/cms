<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',//应用id，必须唯一
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',//控制器命名空间
    'language' => 'zh-CN',//默认语言
    'timeZone' => 'Asia/Shanghai',//默认时区
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'user' => [
            'identityClass' => backend\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_backend_identity'],
            'idParam' => '__backend__id',
            'returnUrlParam' => '_backend_returnUrl',
        ],
        'log' => [//此项具体详细配置，请访问http://wiki.feehi.com/index.php?title=Yii2_log
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,//当触发levels配置的错误级别时，保存到日志文件
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => yii\log\EmailTarget::class,//当触发levels配置的错误级别时，发送到此些邮箱（请改成自己的邮箱）
                    'levels' => ['error', 'warning'],
                    /*'categories' => [//默认匹配所有分类。启用此项后，仅匹配数组中的分类信息会触发邮件提醒（白名单）
                        'yii\db\*',
                        'yii\web\HttpException:*',
                    ],*/
                    'except' => [//以下配置，除了匹配数组中的分类信息都会触发邮件提醒（黑名单）
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\debug\Module:checkAccess',
                    ],
                    'message' => [
                        'to' => ['admin@feehi.com', 'liufee@126.com'],
                        'subject' => '来自 Feehi CMS 后台的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'rbac' => [
            'class' => backend\components\Rbac::class,
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
        'request' => [
            'csrfParam' =>'_csrf_backend',
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
