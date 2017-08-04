<?php

namespace common\libs;

use common\models\UserCenter;
use usercenter\components\exception\Exception;


class UserToken
{

    public static function getToken(){
        $token = isset($_COOKIE[Constant::LOGIN_TOKEN_NAME]) ? $_COOKIE[Constant::LOGIN_TOKEN_NAME] : "";
        if(empty($token)){
            $token = \Yii::$app->request->get('token',"");
        }
        return $token;
    }

    public static function getRealIP($withNginx = true){
        $ip = \Yii::$app->request->userIP;
        $withNginx && isset(\Yii::$app->request->headers['x-real-ip']) && $ip = \Yii::$app->request->headers['x-real-ip'];
        return $ip;
    }


    public static function tokenToUser($token="",$checkIp = true){
        if(empty($token)){
            $token = self::getToken();
        }
        //根据token获取userId
        $token = str_replace(" ", "+", $token);
        $aes = new AES();
        $token = $aes->decode($token);
        $arrToken = explode('||', $token);
        if (count($arrToken) != 8) {
            throw new Exception('登录过期，请重新登录',Exception::ERROR_COMMON);
        }
        list($time,$userId,$passwd,$check,$mobile,$email,$timeRev,$noise) = $arrToken;
        if(strrev($time) != $timeRev){
            throw new Exception('登录失败，请重试',Exception::ERROR_COMMON);
        }
        if($check != md5($userId.$passwd.$mobile.$email.$time,$noise)){
            throw new Exception('登录失败，请重试',Exception::ERROR_COMMON);
        }
        $user = UserCenter::findOneById($userId);
        if(empty($user)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        if($passwd != $user['password'] || $mobile != $user['mobile'] || $email != $user['email']){
            throw new Exception("登录信息已过期，请重新登录",Exception::ERROR_COMMON);
        }
        $allIpArr = [];
        $allIpArr[] = self::getRealIP();
        isset($_SERVER['HTTP_CLIENT_IP']) && $allIpArr[] = $_SERVER['HTTP_CLIENT_IP'];
//        isset(\Yii::$app->request->headers['x-real-ip']) && $allIpArr[] = \Yii::$app->request->headers['x-real-ip'];
        if($checkIp && ($noise != $user['login_ip'] ||  !in_array($user['login_ip'],$allIpArr))){
            //判断是否深蓝平台
            throw new Exception('登录信息已过期，请重试',Exception::ERROR_COMMON);
        }
        $user['name'] = $user['username'];
        $user['user_id'] = $user['id'];
        return $user;
    }

    public static function userToToken($user){
        if(is_numeric($user)){
            $user = UserCenter::findOneById($user);
        }
        if(empty($user)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        $time = strval(time());
        $timeRev = strrev($time);
        $userId = $user['id'];
        $mobile = strval($user['mobile']);
        $email = strval($user['email']);
        $passwd = strval($user['password']);
        $noise = strval($user['login_ip']);
        $check = md5($userId.$passwd.$mobile.$email.$time,$noise);
        $passData = [$time,$userId,$passwd,$check,$mobile,$email,$timeRev,$noise];
        $tokenStrOri = join('||',$passData);
        $aes = new AES;
        $token = $aes->encode($tokenStrOri);
        return $token;
    }
}