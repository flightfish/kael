<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-usercenter',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'common' => [
            'class' => 'usercenter\modules\common\Module',
        ],
        'admin' => [
            'class' => 'usercenter\modules\admin\Module',
        ],
        'user' => [
            'class' => 'usercenter\modules\auth\Module',
        ],
        'auth' => [
            'class' => 'usercenter\modules\auth\Module',
        ],
    ],
    'controllerNamespace' => 'usercenter\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 10 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => ['usercenter'],
                    'logFile' => '@runtime/logs/error.log.'.date('Ymd'),
                    'logVars' => [],
                    'enableRotation'=> false,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['usercenter'],
                    'logFile' => '@runtime/logs/info.log.'.date('Ymd'),
                    'enableRotation'=> false,
                    'logVars' => [], //注释掉这行可以在log中打印$_GET和$_SERVER信息
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'categories' => ['usercenter'],
                    'logFile' => '@runtime/logs/warning.log.'.date('Ymd'),
                    'enableRotation'=> false,
                    'logVars' => [],
                ],
            ],

        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation'=>false
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.exmail.qq.com',
                'username' => 'tikunotice@knowbox.cn',
                'password' => 'Know11',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
    ],
    'params' => $params,
];
