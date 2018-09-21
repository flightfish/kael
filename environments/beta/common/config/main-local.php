<?php
return [
    'components' => [
        'db' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.35.226;port=3339;dbname=usercenter',
            'username' => 'test',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => '10.10.213.219',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
