<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php')
);

$config = [
    'id' => 'app-install',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'install\controllers',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'qxOH-LMMrJJ_unqJzWsPO1eL39JF0cnK',
            'csrfParam' =>'_csrf_install',
        ],
        'i18n' => [
            'translations' => [
                'install*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@install/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'df' => 'install.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
    'on beforeRequest' => function($event) {
        if(isset(\yii::$app->session['language'])) \yii::$app->language = yii::$app->session['language'];
    },
];
if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::className(),
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::className(),
    ];
}
return $config;
