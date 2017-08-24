<?php
return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/main.php',
    require __DIR__ . '/main-local.php',
    require __DIR__ . '/test.php',
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=127.0.0.1;dbname=cms_test',
                'username' => 'root',
                'password' => '',
            ]
        ],
    ]
);
