<?php
return [
    'components' => [
        'db' => [//用户中心
//            'class' => 'yii\db\Connection',
            'class' => 'common\components\mysql\MysqlConnection',
            'dsn' => 'mysql:host=172.16.2.27;port=3364;dbname=usercenter_new2',
            'username' => 'lyj',
            'password' => 'lyj123',
            'charset' => 'utf8',
        ],
        'db_ehr' => [
            'class' => 'common\components\mysql\MysqlConnection',
            'dsn' => 'mysql:host=172.16.2.27;port=3363;dbname=ehr',
            'username' => 'test',
            'password' => 'test',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => '172.16.0.129',
            'port' => 6379,
            'database' => 0,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
    ],
];
