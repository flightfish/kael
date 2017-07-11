<?php
namespace questionmis\controllers;

use questionmis\components\exception\Exception;
use questionmis\components\logs\Log;
use Yii;

use yii\web\Controller;


/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        Log::error($exception);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $array = array(
            "code"=> Exception::SYSTEM_ERROR_CODE,
            "message" => $exception->getMessage(),
            'requestId'   => Yii::$app->params['requestId'],
            'data' => [],
        );
        $response->data = $array;
        $response->send();
    }

    public function actionOpcacheReset(){
        $ret = opcache_reset();
        echo 'opcache reset ['.($ret ? 'OK':'FAIL')."]\n";
    }
}
