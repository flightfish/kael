<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'db_knowbox' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.48.120;port=3309;dbname=knowbox',
            'username' => 'test',
            'password' => 'test',
            'charset' => 'utf8',
        ],
        'db_tiku' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.169.75;port=3306;dbname=knowbox',
            'username' => 'root',
            'password' => 'Knowbox512+_*',
            'charset' => 'utf8',
        ],
    ]
];
