<?php
namespace console\components\logs;

use Yii;
use yii\base\Component;

class Log extends Component
{

    private static $appname = "console";

    public static function info($extra = []){
        $logInfo = [
            'params'       => Yii::$app->request->params,
            'extra'     => $extra
        ];
        Yii::info(json_encode($logInfo), self::$appname);
    }

    public static function error($exception){
        $logInfo = [
            'params'       => Yii::$app->request->params,
            'class'     => get_class($exception),
            'errorCode' => $exception->getCode(),
            'message'   => $exception->getMessage(),
        ];
        Yii::error(json_encode($logInfo), self::$appname);
    }

    public static function infoMessage($message){
        echo $message."\n";
        self::info(['message'=>$message]);
    }
}
