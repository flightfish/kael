<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class BossApi {



    private static function curlApi($path,$data){
        $headers = [
            "Referer: https://bslive.knowbox.cn/",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
            "Sec-Fetch-Mode: cors",
            "Accept: application/json, text/plain, */*",
            "Content-Type: application/json",
        ];
        $dataStr = json_encode($data,64|256);
        $url = \Yii::$app->params['boss_url'].$path;
        $ret = AppFunc::curlPost($url,$dataStr,$headers);
        $retJson = json_decode($ret,true);
        if(empty($retJson) || ($retJson['code']??-1) != 0){
            throw new Exception("BOSS请求失败：".strval($ret));
        }
        return $retJson['data'];
    }

    public static function employeeUpdateJobStatus($kaelId,$type){
        //1 在职 2 离职  3将离职
        return self::curlApi('/employee/updateJobStatus.do',[
            'kaelId'=>$kaelId,
            'type'=>$type,
        ]);
    }


}
