<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class EmailApi{

    const API_GETTOKEN = "https://api.exmail.qq.com/cgi-bin/gettoken";
    const API_USERCREATE = "https://api.exmail.qq.com/cgi-bin/user/create";
    const API_USERUPDATE = "https://api.exmail.qq.com/cgi-bin/user/update";
    const API_USERDELETE = "https://api.exmail.qq.com/cgi-bin/user/delete";
    const API_USERGET = "https://api.exmail.qq.com/cgi-bin/user/get";
    const API_USERBATCHCHECK = "https://api.exmail.qq.com/cgi-bin/user/batchcheck";
    const API_GETDEPARTMENTUSER = "https://api.exmail.qq.com/cgi-bin/user/list";
    const API_GETDEPARTMENTLIST = "https://api.exmail.qq.com/cgi-bin/department/list";

    public static function getAccessToken($id,$secret){
        $retStr = AppFunc::curlGet(self::API_GETTOKEN."?corpid={$id}&corpsecret={$secret}");
        $retJson = json_decode($retStr,true);
        if(empty($retJson) || empty($retJson['access_token'])){
            throw new Exception("[EXMAIL]".$retJson['errmsg'] ?? $retStr);
        }
        return $retJson['access_token'];
    }

    public static function getAccessTokenTXL(){
        $key = 'EMAIL_ACCESS_TOKEN_TXL_'.\Yii::$app->params['qqemail_corpid'];
        $accessToken = Cache::getCacheString($key);
        if(empty($accessToken)){
            $accessToken = self::getAccessToken(\Yii::$app->params['qqemail_corpid'],\Yii::$app->params['qqemail_corpsecret_txl']);
            Cache::setCacheString($key,3600,$accessToken);
        }
        return $accessToken;
    }

    public static function getAccessTokenSSO(){
        $key = 'EMAIL_ACCESS_TOKEN_SSO_'.\Yii::$app->params['qqemail_corpid'];
        $accessToken = Cache::getCacheString($key);
        if(empty($accessToken)){
            $accessToken = self::getAccessToken(\Yii::$app->params['qqemail_corpid'],\Yii::$app->params['qqemail_corpsecret_sso']);
            Cache::setCacheString($key,3600,$accessToken);
        }
        return $accessToken;
    }

    public static function curlTXL($url,$data,$method='POST'){
        $url .= '?access_token='.self::getAccessTokenTXL();
        if($method == 'POST'){
            $retStr = AppFunc::postJson($url,$data);
        }else{
            $dataStr = http_build_query($data);
            !empty($dataStr) && $url = $url.'&'.$dataStr;
            $retStr = AppFunc::curlGet($url);
        }
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[EXMAIL]'.$retJson['errmsg']."[".$retJson['errcode']."]"??"");
        }
        return $retJson;

    }

    public static function addUser($email,$name,$password){
        $data = [
            'userid'=>$email,
            'name'=>$name,
            'department'=>[\Yii::$app->params['qqemail_department']],
            'password'=>$password
        ];
        echo json_encode($data,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
        $ret = self::curlTXL(self::API_USERCREATE,$data);
        return $ret;
    }

    public static function updateUserDepartment($email){
        $data = [
            'userid'=>$email,
            'department'=>[\Yii::$app->params['qqemail_department']],
        ];
        $ret = self::curlTXL(self::API_USERUPDATE,$data);
        return $ret;
    }
    public static function updateUserPassword($email,$password){
        $data = [
            'userid'=>$email,
            'password'=>$password
        ];
        $ret = self::curlTXL(self::API_USERUPDATE,$data);
        return $ret;
    }

    public static function deleteUser($email){
        $data = [
            'userid'=>$email,
        ];
        $ret = self::curlTXL(self::API_USERDELETE,$data,'GET');
        return $ret;
    }

    public static function getUser($email){
        $data = [
            'userid'=>$email,
        ];
        $ret = self::curlTXL(self::API_USERGET,$data,'GET');
        return $ret;
    }

    public static function batchCheck($emailList){
        $data = [
            'userlist'=>$emailList,
        ];
        $ret = self::curlTXL(self::API_USERBATCHCHECK,$data);
        /**
        {
        "errcode": 0,
        "errmsg": "ok",
        "list": [
        {"user":"zhangsan@bjdev.com", "type":1}, 帐号类型。-1:帐号号无效; 0:帐号名未被占用; 1:主帐号; 2:别名帐号; 3:邮件群组帐号
        {"user":"zhangsangroup@shdev.com", "type":3}
        ]
        }
         */
        return $ret;
    }

    public static function getDepartmentListUser(){
        $data = ['department_id'=>\Yii::$app->params['qqemail_department'],'fetch_child'=>0];
        return self::curlTXL(self::API_GETDEPARTMENTUSER,$data,'GET');
    }

    public static function getDepartmentList(){
        return self::curlTXL(self::API_GETDEPARTMENTLIST,[],'GET');
    }
}
