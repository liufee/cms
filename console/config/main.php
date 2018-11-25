<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

defined('SWOOLE_PROCESS') or define('SWOOLE_PROCESS', 1);
defined('SWOOLE_TCP') or define('SWOOLE_TCP', 1);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => yii\log\FileTarget::className(),
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            'class' => yii\web\Session::className()
        ]
    ],
    'controllerMap'=>[
        'serve' => [
            'class' => yii\console\controllers\ServeController::className(),
            'docroot' => '@frontend/web',
        ],
    ],
    'params' => $params,
];
