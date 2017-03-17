<?php
$config = [
    'name' => 'Feehi CMS',
    'version' => '0.0.9',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=feehi',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'formatter' => [
            'dateFormat' => 'php:Y-m-d H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CHY',
            'nullDisplay' => '-',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,//false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.feehi.com',  //每种邮箱的host配置不一样
                'username' => 'admin@feehi.com',
                'password' => 'password',
                'port' => '586',
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['admin@feehi.com' => 'Feehi CMS robot ']
            ],
        ],
        'view' => [
            'class' => 'feehi\components\View',
        ],
        'feehi' => [
            'class' => 'feehi\components\Feehi',
        ],
    ],
];
$install = yii::getAlias('@common/config/conf/db.php');
if( file_exists($install) ){
    return yii\helpers\ArrayHelper::merge($config, (require $install));
}
return $config;