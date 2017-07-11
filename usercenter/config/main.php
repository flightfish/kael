<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-questionmis',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'common' => [
            'class' => 'questionmis\modules\common\Module',
        ],
        //entrystore
        'entry_common' => [
            'class' => 'questionmis\modules\entrystore\common\Module',
        ],
        'entry_task' => [
            'class' => 'questionmis\modules\entrystore\task\Module',
        ],
        'entry_user' => [
            'class' => 'questionmis\modules\entrystore\user\Module',
        ],
        'entry_question' => [
            'class' => 'questionmis\modules\entrystore\question\Module',
        ],
        'entry_word' => [
            'class' => 'questionmis\modules\entrystore\word\Module',
        ],
        //qualitysys
        'quality_user' => [
            'class' => 'questionmis\modules\qualitysys\user\Module',
        ],
        'quality_question' => [
            'class' => 'questionmis\modules\qualitysys\question\Module',
        ],
        'quality_common' => [
            'class' => 'questionmis\modules\qualitysys\common\Module',
        ],
        //bookstore
        'book_user' => [
            'class' => 'questionmis\modules\bookstore\user\Module',
        ],
        'book_assist' => [
            'class' => 'questionmis\modules\bookstore\assist\Module',
        ],
        'book_paper' => [
            'class' => 'questionmis\modules\bookstore\paper\Module',
        ],
        'book_common' => [
            'class' => 'questionmis\modules\bookstore\common\Module',
        ],
        'book_questionall' => [
            'class' => 'questionmis\modules\bookstore\questionall\Module',
        ],
        //admin
        'admin_bookstore' => [
            'class' => 'questionmis\modules\admin\bookstore\Module',
        ],
        'admin_qualitysys' => [
            'class' => 'questionmis\modules\admin\qualitysys\Module',
        ],
        'admin_qselect' => [
            'class' => 'questionmis\modules\admin\qselect\Module',
        ],
        'admin' => [
            'class' => 'questionmis\modules\admin\Module',
        ],
        'know_know' => [
            'class' => 'questionmis\modules\knowledge\knowledge\Module',
        ],
        'issue_issue' => [
            'class' => 'questionmis\modules\issue\issue\Module',
        ],
        'qselect_user' => [
            'class' => 'questionmis\modules\qselect\user\Module',
        ],
        //qselect
        'qselect_select' => [
            'class' => 'questionmis\modules\qselect\select\Module',
        ],
        'qselect_task' => [
            'class' => 'questionmis\modules\qselect\task\Module',
        ],
        'qselect_common' => [
            'class' => 'questionmis\modules\qselect\common\Module',
        ],
    ],
    'controllerNamespace' => 'questionmis\controllers',
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
                    'categories' => ['questionmis'],
                    'logFile' => '@runtime/logs/error.log.'.date('Ymd'),
                    'logVars' => [],
                    'enableRotation'=> false,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['questionmis'],
                    'logFile' => '@runtime/logs/info.log.'.date('Ymd'),
                    'enableRotation'=> false,
                    'logVars' => [], //注释掉这行可以在log中打印$_GET和$_SERVER信息
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'categories' => ['questionmis'],
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
