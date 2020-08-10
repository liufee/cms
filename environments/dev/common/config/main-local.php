<?php
return [
    'components' => [
        /**
         * dsn:
         *  - mysql mysql:host=localhost;dbname=feehi
         *  - sqlite sqlite:/feehi.db
         */
        'db' => [
            'class' => yii\db\Connection::className(),
            'dsn' => 'sqlite:/feehi.db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => "",
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
