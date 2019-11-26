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
        'dbpool_live'=>[
            'class' => 'yii\db\Connection',
            'dsn'=>'mysql:host=172.16.2.176;port=3364;dbname=bukexuetang',
            'username'=>'liyuan',
            'password'=>'Liyuan@0123',
            'charset'=>'utf8mb4',
        ],
        'redis' => [
            'class' => 'usercenter\components\cache\Redis',
            'hostname' => '172.16.1.83',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
