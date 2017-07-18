<?php
namespace usercenter\lib;

use Yii;
use yii\web\Request;

class Log
{
    /*
     *  错误日志记录
     *  param:
     *  errcode 错误码
     *  info 错误信息
     *  level 错误级别(当大于0时写库)
     * @return true on succ, array of error info on fail
     */
    public static function LogError($errcode, $info, $level=0)
    {
        $host = Yii::$app->request->getHostInfo();
        $url = Yii::$app->request->url;
        $param = Yii::$app->request->getQueryParams();
        $body = Yii::$app->request->getBodyParams();
        $source = "";
        if(isset($param['source']))
            $source = $param['source'];
//        $userID = Yii::$app->request->getUserID();
//        $userType = Yii::$app->request->getUserType();
        $info=is_array($info)?json_encode($info):$info;
        $content = $source."\t".$host."\t".$url."\t".json_encode($param)."\t".json_encode($body)."\t".$errcode."\t".$info;
        if($level == 0)
            Yii::warning($content, "knowbox");
        else
            Yii::error($content, "knowbox");
        return true;
    }
    
    /*
     *  访问日志记录
     *  param:
     *  $timeSpan 响应时间
     *  level 错误级别(当大于0时写库)
     * @return true on succ, array of error info on fail
     */
    public static function LogRequest($timeSpan)
    {
        $host = Yii::$app->request->getHostInfo();
        $url = Yii::$app->request->url;
        $param = Yii::$app->request->getQueryParams();
        $body = Yii::$app->request->getBodyParams();
        $source = "";
        if(isset($param['source']))
            $source = $param['source'];
//        $userID = Yii::$app->request->getUserID();
//        $userType = Yii::$app->request->getUserType();

//        $content = "INNERACCESS\t".$timeSpan."\t".$source."\t".$userID."\t".$userType."\t".$host."\t".$url."\t".json_encode($param)."\t".json_encode($body);
        $content = "INNERACCESS\t".$timeSpan."\t".$source."\t".$host."\t".$url."\t".json_encode($param)."\t".json_encode($body);
        Yii::info($content, "knowbox");
        return true;
    }
}
