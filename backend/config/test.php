<?php
return [
    'id' => 'app-backend-tests',
    'language' => 'zh-CN',
    'components' => [
        'assetManager' => [
            'basePath' => '@admin/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'enableAutoLogin' => true,
            'autoRenewCookie' => false,
        ],
        'request' => [
            'enableCsrfValidation' => false,
        ]
    ],
];
