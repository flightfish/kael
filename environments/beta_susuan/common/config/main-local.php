<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.35.226;port=3339;dbname=usercenter_susuan',
            'username' => 'test',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'db_base' => [//基础库
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.35.226;port=3339;dbname=knowboxstore_susuan',
            'username' => 'test',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'db_user' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.35.226;port=3339;dbname=usercenter',
            'username' => 'test',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'db_business' => [//业务库 只读
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.35.226;port=3339;dbname=susuan_online',
            'username' => 'test',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => 'localhost',
            'port' => 6397,
            'database' => 0,
        ],
    ],
];
