<?php

$requestUri = $_SERVER["REQUEST_URI"];
if(empty($requestUri)|| $requestUri == '/'){
    header('Location:/html/index.html');
    exit();
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

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

if(!empty($_SERVER['HTTP_ORIGIN'])){
    $origin = $_SERVER['HTTP_ORIGIN'];
    header("Access-Control-Allow-Origin:{$origin}");
}
//header("Access-Control-Allow-Method:GET,POST,OPTIONS");
header("Access-Control-Allow-Headers:x-requested-with,content-type,authorization");

$application->run();
