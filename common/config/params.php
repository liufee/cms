<?php
return [
    'supportEmail' => 'admin@feehi.com',
    'user.passwordResetTokenExpire' => 3600,
    'site' => [
        'url' => 'http://cms.feehi.com',
        'sign' => '###~SITEURL~###',//数据库中保存的本站地址，展示时替换成正确url
    ],
    'admin' => [
        'url' => 'http://admin.cms.feehi.com',
    ]
];
