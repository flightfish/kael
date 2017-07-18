<?php
namespace usercenter\components\logs;

use usercenter\components\exception\Exception;
use Yii;
use yii\base\Component;

class Log extends Component
{

    private static $appname = "usercenter";

    public static function info($extra = []){
        if (!Yii::$app->request->hasProperty('url')) return;
        $logInfo = [
            'url'       => Yii::$app->request->url,
            'requestId' => Yii::$app->params['requestId'],
            'params'    => Yii::$app->request->get(),
            'postData'    => Yii::$app->request->post(),
            'extra'     => $extra
        ];
        $type = "INFO";
        Yii::info($type."\t".json_encode($logInfo), self::$appname);
    }

    public static function error(\Exception $exception){
        if (!Yii::$app->request->hasProperty('url')) return;
        if($exception instanceof Exception){
            $logInfo = [
                'url'       => Yii::$app->request->url,
                'requestId' => Yii::$app->params['requestId'],
                'params'    => Yii::$app->request->get(),
                'postData'    => Yii::$app->request->post(),
                'class'     => get_class($exception),
                'errorCode' => $exception->getCode(),
                'message'   => $exception->getMessage()
            ];
            $type = "CUSTOM_ERROR";
        }else{
            $logInfo = [
                'url'       => Yii::$app->request->url,
                'requestId' => Yii::$app->params['requestId'],
                'params'    => Yii::$app->request->get(),
                'postData'    => Yii::$app->request->post(),
                'class'     => get_class($exception),
                'errorCode' => $exception->getCode(),
                'message'   => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace'  => $exception->getTrace(),
            ];
            $type = "SYS_ERROR";
        }
        Yii::error($type."\t".json_encode($logInfo), self::$appname);
    }
}
