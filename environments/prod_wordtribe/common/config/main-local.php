<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.19.141.31;port=3312;dbname=questionmis',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'db_base' => [//基础库
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.19.141.31;port=3311;dbname=knowboxstore',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'db_user' => [//用户中心
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.19.141.31;port=3310;dbname=usercenter',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'db_business' => [//业务库 只读
            'class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=10.9.93.244;port=3306;dbname=susuan',
            'dsn' => 'mysql:host=10.19.105.13;port=3310;dbname=word_tribe',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'questionmis\components\cache\Redis',
            'hostname' => 'localhost',
            'port' => 6397,
            'database' => 0,
        ],
    ],
];
