<?php
return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => yii\web\User::className(),
            'identityClass' => backend\models\User::className(),
        ],
    ],
];
