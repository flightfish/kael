<?php

$requestUri = $_SERVER["REQUEST_URI"];
if(empty($requestUri)|| $requestUri == '/'){
    header('Location:/login.html');
    exit();
}
//if(false === stripos($requestUri,'html') && false === stripos($requestUri,'admin') && false === stripos($requestUri,'token')){
//    header('Location:/login.html');
//    exit();
//}


defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');
//require(__DIR__ . '/../config/constant.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
