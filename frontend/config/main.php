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
            'identityClass' => common\models\User::className(),
            'enableAutoLogin' => true,
        ],
        'session' => [
            'timeout' => 1440,//session expiration, unit: seconds. session过期时间，单位为秒
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
                    /*
                        when occurs {levels} level error, will send you a email to {message.to} (当触发levels配置的错误级别时，发送到{message.to}配置的邮箱中)

                        Attention(特别注意): If you no need error message send to email, remove this configuration  (如您不需要发送邮件提醒建议删除此配置)

                        1. If enabled send log to email, ensure the correct SMTP configuration. or may, when a page occurs {levels} level log, they will response html after success send email or until connect to SMTP server timeout.
                        2. If common/config/main.php mail.useFileTransport is true, they will no send email. just write email to runtime directory, may exhaust hard disk
                        1.当打开的页面包含错误时，会等到发完邮件才响应html。若配置的SMTP地址有误，将会一直等待连接SMTP服务器超时后才响应。
                        2.如果common/config/main.php mail.useFileTransport为true时，并不会真发邮件，只把邮件写到runtime目录，很容易造成几十个G吃硬盘。
                     */
                    'class' => yii\log\EmailTarget::className(),
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\debug\Module::checkAccess',
                    ],
                    'message' => [
                        'to' => ['alert1@xxx.com', 'alert2@xxx.com'],
                        'subject' => '来自 Feehi CMS 前台的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'cache' => [
            'class' => yii\caching\FileCache::className(),
            'keyPrefix' => 'frontend',
        ],
        'urlManager' => [
            /*
               - true url like xxx.com/controller/action (need your website enable url rewrite, more see http://doc.feehi.com/install.html)
               - false url like xxx.com/index.php?r=controller/action

               - true url格式如xxx.com/controller/action  (需要配合web服务器配置伪静态，详见http://doc.feehi.com/install.html)
               - false url格式如xxx.com/index.php?r=controller/action
            */
            'enablePrettyUrl' => false,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            //'suffix' => '.html',
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
                frontend\assets\AppAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'css' => [
                        'a' => 'css/style.css',
                        'b' => 'plugins/toastr/toastr.min.css',
                    ],
                    'js' => [
                        'a' => 'js/index.js',
                        'b' => 'plugins/toastr/toastr.min.js',
                    ],
                ],
                frontend\assets\IndexAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'js' => [
                        'a' => 'js/jquery.min.js',
                        'b' => 'js/responsiveslides.min.js',
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
    'on beforeRequest' => [common\components\Feehi::className(), 'frontendInit'],
];
