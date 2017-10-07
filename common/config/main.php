<?php
$config = [
    'name' => 'Feehi CMS',
    'version' => '1.0.0-alpha3',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [
            'class' => yii\db\Connection::className(),
            'dsn' => 'mysql:host=localhost;dbname=feehi',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::className(),
        ],
        'formatter' => [
            'dateFormat' => 'php:Y-m-d H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CHY',
            'nullDisplay' => '-',
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::className(),
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
            'class' => feehi\components\View::className(),
        ],
        'feehi' => [
            'class' => feehi\components\Feehi::className(),
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::className(),
        ],
    ],
];
$install = yii::getAlias('@common/config/conf/db.php');
if( file_exists($install) ){
    return yii\helpers\ArrayHelper::merge($config, (require $install));
}
return $config;