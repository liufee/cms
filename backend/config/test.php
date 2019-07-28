<?php
return [
    'id' => 'app-backend-tests',
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
    ],
];
