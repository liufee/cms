<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
    ],
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
        'generators' => [
            'crud' => [
                'class' => yii\gii\generators\crud\Generator::className(),
                'templates' => [
                    'default' => '@backend/components/gii/crud/default',
                    'yii' => '@vendor/yiisoft/yii2-gii/generators/crud/default',
                ]
            ]
        ],
    ];
}

return $config;
