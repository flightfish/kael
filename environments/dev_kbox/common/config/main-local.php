<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3308;dbname=entrystore_3',
            'username' => 'lyj',
            'password' => 'lyj123',
            'charset' => 'utf8',
        ],
        'db_base' => [//基础库
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3308;dbname=knowboxstore_2',
            'username' => 'lyj',
            'password' => 'lyj123',
            'charset' => 'utf8',
        ],
        'db_user' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3308;dbname=usercenter_3',
            'username' => 'lyj',
            'password' => 'lyj123',
            'charset' => 'utf8',
        ],
        'db_business' => [//业务库 只读
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3309;dbname=knowbox',
            'username' => 'test',
            'password' => 'test',
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
