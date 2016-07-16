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
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
            'keyPrefix' => 'frontend',       // 唯一键前缀
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,//隐藏index.php
            'enableStrictParsing' => false,
            //'suffix' => '.html',//后缀，如果设置了此项，那么浏览器地址栏就必须带上.html后缀，否则会报404错误
            'rules' => [
                //'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                //'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>?id=<id>'
                //'detail/<id:\d+>' => 'site/detail?id=$id',
                //'post/22'=>'site/detail',
                //'<controller:detail>/<id:\d+>' => '<controller>/index',
                '' => 'site/index',
                '<controller:w+>/<action:\w+>'=>'<controller>/<action>',
                '<page:\d+>' => 'site/index',
                'login' => 'site/login',
                'sinup' => 'site/signup',
                'about|contact' => 'page/view',
                'page/<name:\w+>' => 'page/view',
                'view/<id:\d+>' => 'article/view',
                'comment' => 'article/comment',
                'article/view/id/<id:\d+>' => 'article/view',
                'Index/detail/id/<id:\d+>' => 'site/detail',
                'search' => 'search/index',
                'article/<cat:\w+>' => 'article/index',
                'list/<page:\d+>' => 'site/index',
                'python|java|javascript' => 'article/index',
            ],
        ],
        'feehi' => [
            'class' => 'feehi\components\Feehi',
        ]
    ],
    'params' => $params,
    'on beforeRequest' => function($event){
        \feehi\components\Feehi::setConfig();
        if(isset(\yii::$app->session['view'])) \yii::$app->viewPath = dirname(__DIR__).'/'.\yii::$app->session['view'];
    }
];
