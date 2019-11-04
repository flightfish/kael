<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class IOApi {

    const API_UPDATE_USER_JD = '/aiou/updateEmployeeForBase.do';//更新员工基地名称



    private static function curlApi($path,$data){

        $url = \Yii::$app->params['io_url'].$path;
        $ret = AppFunc::postJson($url,$data);
        $retJson = json_decode($ret,true);
        if(empty($retJson) || $retJson['code'] != 0){
            throw new Exception("IO请求失败：".strval($ret));
        }
        return $retJson['data'];
    }

    public static function updateUserBaseName($kaelId,$baseName){
        return self::curlApi(self::API_UPDATE_USER_JD,[
            'kaelId'=>$kaelId,
            'baseName'=>$baseName,
        ]);
    }


}
