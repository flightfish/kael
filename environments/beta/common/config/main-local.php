<?php
return [
    'components' => [
        'db' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=172.16.2.176;port=3339;dbname=usercenter',
            'username' => 'test',
            'password' => 'qaOnly!@#',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => '172.16.1.83',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
