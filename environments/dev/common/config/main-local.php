<?php
return [
    'components' => [
        'db' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3308;dbname=usercenter_new',
            'username' => 'lyj',
            'password' => 'lyj123',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => 'localhost',
            'port' => 6397,
            'database' => 0,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
    ],
];
