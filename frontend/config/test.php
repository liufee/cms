<?php
return [
    'id' => 'app-frontend-tests',
    'language' => 'zh-CN',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => true,
            'rules' => []
        ],
        'request' => [
            'enableCsrfValidation' => false,
        ]
    ],
];
