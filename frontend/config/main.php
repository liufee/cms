<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'article/index',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => frontend\models\User::className(),
            'enableAutoLogin' => true,
        ],
        'session' => [
            'timeout' => 1440,//session过期时间，单位为秒
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::className(),
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Y/m/d') . '.log',
                ],
                [
                    /**
                     注：此配置可能造成：
                         1.当打开的页面包含错误时，响应缓慢。若您配置的发件箱不存在或连不上一直等待超时。
                         2.如果common/config/main.php mail useFileTransport为true时，并不会真发邮件，只把邮件写到runtime目录，很容易造成几十个G吃硬盘。
                         如您不需要发送邮件提醒建议删除此配置
                     */
                    'class' => yii\log\EmailTarget::className(),
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\debug\Module::checkAccess',
                    ],
                    'message' => [
                        'to' => ['admin@feehi.com', 'liufee@126.com'],//当触发levels配置的错误级别时，发送到此些邮箱（请改成自己的邮箱）
                        'subject' => '来自 Feehi CMS 前台的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'cache' => [
            'class' => yii\caching\FileCache::className(),//使用文件缓存，可根据需要改成apc redis memcache等其他缓存方式
            'keyPrefix' => 'frontend',       // 唯一键前缀
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,//true 美化路由(注:需要配合web服务器配置伪静态，详见http://doc.feehi.com/install.html), false 不美化路由
            'showScriptName' => false,//隐藏index.php
            'enableStrictParsing' => false,
            //'suffix' => '.html',//后缀，如果设置了此项，那么浏览器地址栏就必须带上.html后缀，否则会报404错误
            'rules' => [
                //'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                //'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>?id=<id>'
                //'detail/<id:\d+>' => 'site/detail?id=$id',
                //'post/22'=>'site/detail',
                //'<controller:detail>/<id:\d+>' => '<controller>/index',
                '' => 'article/index',
                '<page:\d+>' => 'article/index',
                'login' => 'site/login',
                'signup' => 'site/signup',
                'view/<id:\d+>' => 'article/view',
                'page/<name:\w+>' => 'page/view',
                'comment' => 'article/comment',
                'search' => 'search/index',
                'tag/<tag:[- \w]+>' => 'search/tag',
                'rss' => 'article/rss',
                'list/<page:\d+>' => 'site/index',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',

                    ],
                ],
                'front*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@frontend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'frontend' => 'frontend.php',
                        'app/error' => 'error.php',

                    ],
                ],
            ],
        ],
        'assetManager' => [
            'linkAssets' => false,
            'bundles' => [
                yii\web\JqueryAsset::className() => [
                    'js' => [],
                ],
                frontend\assets\AppAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'css' => [
                        'a' => 'css/style.css',
                        'b' => 'plugins/toastr/toastr.min.css',
                    ],
                    'js' => [
                        'a' => 'js/jquery.min.js',
                        'b' => 'js/index.js',
                        'c' => 'plugins/toastr/toastr.min.js',
                    ],
                ],
                frontend\assets\IndexAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'js' => [
                        'a' => 'js/responsiveslides.min.js',
                    ]
                ],
                frontend\assets\ViewAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'css' => [
                        'a' => 'syntaxhighlighter/styles/shCoreDefault.css'
                    ],
                    'js' => [
                        'a' => 'syntaxhighlighter/scripts/shCore.js',
                        'b' => 'syntaxhighlighter/scripts/shBrushJScript.js',
                        'c' => 'syntaxhighlighter/scripts/shBrushPython.js',
                        'd' => 'syntaxhighlighter/scripts/shBrushPhp.js',
                        'e' => 'syntaxhighlighter/scripts/shBrushJava.js',
                        'f' =>'syntaxhighlighter/scripts/shBrushCss.js',
                    ]
                ],
            ]
        ]
    ],
    'params' => $params,
    'on beforeRequest' => [feehi\components\Feehi::className(), 'frontendInit'],
];
