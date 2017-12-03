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
            'identityClass' => backend\models\User::className(),
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_backend_identity'],
            'idParam' => '__backend__id',
            'returnUrlParam' => '_backend_returnUrl',
        ],
        'session' => [
            'timeout' => 1440,//session过期时间，单位为秒
        ],
        'log' => [//此项具体详细配置，请访问http://wiki.feehi.com/index.php?title=Yii2_log
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::className(),//当触发levels配置的错误级别时，保存到日志文件
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => yii\log\EmailTarget::className(),//当触发levels配置的错误级别时，发送到此些邮箱（请改成自己的邮箱）
                    'levels' => ['error', 'warning'],
                    /*'categories' => [//默认匹配所有分类。启用此项后，仅匹配数组中的分类信息会触发邮件提醒（白名单）
                        'yii\db\*',
                        'yii\web\HttpException:*',
                    ],*/
                    'except' => [//以下配置，除了匹配数组中的分类信息都会触发邮件提醒（黑名单）
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\debug\Module::checkAccess',
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
        'request' => [
            'csrfParam' =>'_csrf_backend',
        ],
        'i18n' => [
            'translations' => [//多语言包设置
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'menu' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'zh-CN',
                    'fileMap' => [
                        'app' => 'menu.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                backend\assets\AppAsset::className() => [
                    'css' => [
                        'a' => 'static/css/bootstrap.min14ed.css?v=3.3.6',
                        'b' => 'static/css/font-awesome.min93e3.css?v=4.4.0',
                        'c' => 'static/css/animate.min.css',
                        'd' => 'static/css/style.min862f.css?v=4.1.0',
                        'e' => 'static/css/plugins/sweetalert/sweetalert.css',
                        'f' => 'static/js/plugins/layer/laydate/need/laydate.css',
                        'g' => 'static/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
                        'h' => 'static/css/plugins/toastr/toastr.min.css',

                    ],
                    'js' => [
                        'a' => 'static/js/feehi.js',
                        'b' => 'static/js/plugins/sweetalert/sweetalert.min.js',
                        'c' => 'static/js/plugins/layer/laydate/laydate.js',
                        'd' => 'static/js/plugins/layer/layer.min.js',
                        'e' => 'static/js/plugins/prettyfile/bootstrap-prettyfile.js',
                        'f' => 'static/js/plugins/toastr/toastr.min.js',
                    ],
                ],
                backend\assets\IndexAsset::className() => [
                    'css' => [
                        'a' => 'static/css/bootstrap.min.css',
                        'b' => 'static/css/font-awesome.min93e3.css?v=4.4.0',
                        'c' => 'static/css/style.min862f.css?v=4.1.0',
                    ],
                    'js' => [
                        'a' => "static/js/jquery.min.js?v=2.1.4",
                        'b' => "static/js/bootstrap.min.js?v=3.3.6",
                        'c' => "static/js/plugins/metisMenu/jquery.metisMenu.js",
                        'd' => "static/js/plugins/slimscroll/jquery.slimscroll.min.js",
                        'e' => "static/js/plugins/layer/layer.min.js",
                        'f' => "static/js/hplus.min.js?v=4.1.0",
                        'g' => "static/js/contabs.min.js",
                        'h' => "static/js/plugins/pace/pace.min.js",
                    ]
                ],
                backend\assets\UeditorAsset::className() => [
                    'css' => [
                        'a' => 'ueditor.all.min.js'
                    ],
                ],
            ]
        ],
    ],
    'on beforeRequest' => [feehi\components\Feehi::className(), 'backendInit'],
    'as access' => [
        'class' => backend\components\AccessControl::className(),
        'allowActions' => [
            'site/login',
            'site/captcha',
            'site/error',
            'site/index',
            'site/main',
            'site/logout',
            'site/language',
            'admin-user/request-password-reset',
            'admin-user/reset-password',
            'admin-user/update-self',
            'debug/*',
            'gii/*',
        ],
        'superAdminUserIds' => [1],//超级管理员用户id，拥有所有权限，不受权限管理的控制
    ],
    'params' => $params,
];
