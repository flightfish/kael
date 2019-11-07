<?php
return [
    'components' => [
        'db' => [//用户中心
            'class' => 'common\components\mysql\MysqlConnection',
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
        ],
        'db_ehr' => [
            'class' => 'common\components\mysql\MysqlConnection',
            'dsn' => 'mysql:host=10.19.141.31;port=3321;dbname=ehr',
            'username' => 'ehrRoot',
            'password' => 'ehrPassword',
            'charset' => 'utf8',
        ],
        'db_live' => [
            'class' => 'common\components\mysql\MysqlConnection',
            'dsn' => 'mysql:host=10.9.89.16;port=3323;dbname=bukexuetang',
            'username' => 'ehrRoot',
            'password' => 'ehrPassword',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' =>false,//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qiye.aliyun.com',  //每种邮箱的host配置不一样
//                'username' => 'mail_service@knowbox.cn',
                'username' => 'mail_service@knowbox.cn',
//                'password' => 'Know11',
                'password' => 'Know11!@#',
                'port' => '465',
                'encryption' => 'ssl', //加密
            ]
        ],
    ],
];
