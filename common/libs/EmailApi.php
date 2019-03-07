<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class EmailApi{

    const API_GETTOKEN = "https://api.exmail.qq.com/cgi-bin/gettoken";
    const API_USERCREATE = "https://api.exmail.qq.com/cgi-bin/user/create?access_token=ACCESS_TOKEN";

    public static function getAccessToken($id,$secret){
        $retStr = AppFunc::curlGet(self::API_GETTOKEN."?corpid={$id}&corpsecret={$secret}");
        $retJson = json_decode($retStr,true);
        if(empty($retJson) || empty($retJson['access_token'])){
            throw new Exception("[EXMAIL]".$retJson['errmsg'] ?? $retStr);
        }
        return $retJson;
    }

    public static function getAccessTokenTXL(){
        return self::getAccessToken(\Yii::$app->params['qqemail_corpid'],\Yii::$app->params['qqemail_corpsecret_txl']);
    }

    public static function getAccessTokenSSO(){
        return self::getAccessToken(\Yii::$app->params['qqemail_corpid'],\Yii::$app->params['qqemail_corpsecret_sso']);
    }

    public static function addUser($email,$name,$password){
        $url = self::API_USERCREATE.'?access_token='.self::getAccessTokenTXL();
        $data = [
            'userid'=>$email,
            'name'=>$name,
            'department'=>\Yii::$app->params['qqemail_department'],
            'password'=>$password
        ];
    }
}
