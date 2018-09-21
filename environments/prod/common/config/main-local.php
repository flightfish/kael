<?php
return [
    'components' => [
        'db' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.19.141.31;port=3310;dbname=usercenter',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => '10.10.217.195',
            'port' => 6379,
            'database' => 0,
            'password' => 'KBRedispt'
        ]
    ],
];
