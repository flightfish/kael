<?php
namespace common\libs;

class EmailApi{

    const API_TOKEN = "https://exmail.qq.com/cgi-bin/token";
    const API_GET = "https://exmail.qq.com/openapi/user/get";
    const API_SYNC = "https://exmail.qq.com/openapi/user/sync";

    public static function getAccessToken(){
        $clientId = 'biz0876xa';
        $clientSecret = 'yuw_0dfuxUa';
        $postData = http_build_query([
            'grant_type'=>'client_credentials',
            'client_id'=>$clientId,
            'client_secret'=>$clientSecret
        ]);
        $ret = AppFunc::curlPost(self::API_TOKEN,$postData);
        $ret = json_decode($ret,true);
        $accessToken = $ret['access_token'];
        return $accessToken;
    }

    public static function curlApi($url,$data = []){
        $accessToken = self::getAccessToken();
        $postData = http_build_query($data);
        $ret = AppFunc::curlPost($url,$postData,['Authorization: Bearer '.$accessToken]);
        $ret = json_decode($ret,true);
        return $ret;
    }

    public static function get($email){
        return self::curlApi(self::API_GET,[
            'alias'=>$email
        ]);
    }

    public static function add($email,$name,$gender,$passwd){
        return self::curlApi(self::API_SYNC,[
            'action'=>2, //1删 2加 3改
            'alias'=>$email,
            'name'=>$name,
            'gender'=>$gender,//1男  2女
            'password'=>$passwd,
        ]);
    }

    public static function del($email){
        return self::curlApi(self::API_SYNC,[
            'action'=>1, //1删 2加 3改
            'alias'=>$email,
        ]);
    }
}
