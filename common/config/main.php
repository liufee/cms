<?php
return [
    /*
        all theses below configuration will be covered by [backend|frontend|api|console]/main-[local].php and backend admin user filled in, the priority is:

        以下配置将会被[backend|frontend|api|console]/main-[local].php以及后台管理页面配置的覆盖。优先顺序如下:

        1. backend admin user filled in at admin web page.
        2. [backend|frontend|api|console]/main-local.php.
        3. [backend|frontend|api|console]/main.php
        4. main-local.php
        5. main.php
    */
    'name' => 'Feehi CMS',
    'version' => '2.1.0.2',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => array_merge([
        'db' => [//database config, will be covered by backend|frontend|api]/main-[local].php
            'class' => yii\db\Connection::className(),
            'dsn' => 'mysql:host=localhost;dbname=feehi',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'cdn' => [//support Qiniu(七牛) TencentCloud(腾讯云) Aliyun(阿里云) Netease(网易云) more detail for visit http://doc.feehi.com/cdn.html
            'class' => feehi\cdn\DummyTarget::className(),//不使用cdn
        ],
        'cache' => [//cache component more detail for visit http://doc.feehi.com/configs.html
            'class' => yii\caching\FileCache::className(),//use file cache, also can replace with redis or other
        ],
        'formatter' => [//global display format configuration
            'dateFormat' => 'php:Y-m-d H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CHY',
            'nullDisplay' => '-',
        ],
        'mailer' => [
            /* Attention(特别注意):
                email sender, will be covered by backend|frontend|api|console]/main-[local].php, and backend admin user filled in "/admin/index.php?setting/website". backend admin user filled in own most priority.

                邮件发送者配置，将会被backend|frontend|api|console]/main-[local].php和后台管理页面"/admin/index.php?setting/website"填入的覆盖。管理页面填入的拥有最高的优先级。
            */
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            /* Attention(特别注意):
                        if useFileTransport was true, they will not send email, just write to directory runtime. they may cause takes up a lot of disk space.
                        if was false, when you configured a none exists SMTP server ip or other error occurs, html page will be block until connect to SMTP timeout.

                        如果useFileTransport为true，并不会真发邮件，只会把邮件写入runtime目录，很有可能造成您的磁盘使用飙升。
                        如果为false，当您配置的STMP地址不存在或错误，页面会一直等到连接邮件服务器超时才会输出页面。
            */
            'useFileTransport' => true,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.feehi.com',
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
        'feehi' => [
            'class' => common\components\Feehi::className(),
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::className(),
        ],
        'assetManager' => [
            /*
             * if was true, will not copy js/css files to @web/assets @web/admin/assets. just create a symbolic link. NOT SUPPORT WINDOWS(windows cannot create symbolic link)
             * if was false, will copy js/css files to  @web/assets @web/admin/assets.
             *
             * 如果为true，将不会拷贝js/css文件到@web/assets @web/admin/assets。而是生成一个软链接。切记不支持WINDOWS(windows不能创建软链接)
             * 如果为false，将会拷贝js/css文件到@web/assets @web/admin/assets
             */
            'linkAssets' => false,
            /*
             * this is always used for config CDN for load js/css. these configs will replace [backend/frontend]/assets/XXXAsset.php public $css | public $js properties
             * 这个配置通常用于配置资源文件(js/css)走CDN. 这些配置将会替换掉[backend/frontend]/assets/XXXAsset.php public $css | public $js的属性值
             */
            'bundles' => [
                yii\widgets\ActiveFormAsset::className() => [
                    'js' => [
                        'a' => 'yii.activeForm.js'
                    ],
                ],
                yii\bootstrap\BootstrapAsset::className() => [
                    'css' => [],
                    'sourcePath' => null,
                ],
                yii\captcha\CaptchaAsset::className() => [
                    'js' => [
                        'a' => 'yii.captcha.js'
                    ],
                ],
                yii\grid\GridViewAsset::className() => [
                    'js' => [
                        'a' => 'yii.gridView.js'
                    ],
                ],
                yii\web\JqueryAsset::className() => [
                    'js' => [
                        'a' => 'jquery.js'
                    ],
                ],
                yii\widgets\PjaxAsset::className() => [
                    'js' => [
                        'a' => 'jquery.pjax.js'
                    ],
                ],
                yii\web\YiiAsset::className() => [
                    'js' => [
                        'a' => 'yii.js'
                    ],
                ],
                yii\validators\ValidationAsset::className() => [
                    'js' => [
                        'a' => 'yii.validation.js'
                    ],
                ],
            ],
        ],
    ], require "services.php")
];