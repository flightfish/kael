<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        
    ],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['console'],
                    'logFile' => '@runtime/logs/error.log.'.date('Ymd'),
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['console'],
                    'logFile' => '@runtime/logs/info.log.'.date('Ymd'),
                    'logVars' => [], //注释掉这行可以在log中打印$_GET和$_SERVER信息
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'categories' => ['console'],
                    'logFile' => '@runtime/logs/warning.log.'.date('Ymd'),
                    'logVars' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
