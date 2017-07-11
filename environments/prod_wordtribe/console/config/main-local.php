<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'db_knowbox' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.37.162;port=3306;dbname=knowbox',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
        'db_tiku' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.9.41.75;port=3309;dbname=knowbox',
            'username' => 'tkapi',
            'password' => 'TK5gFp6HO',
            'charset' => 'utf8',
        ],
    ]
];
