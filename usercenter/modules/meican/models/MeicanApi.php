<?php

namespace usercenter\modules\meican\models;



use common\libs\AppFunc;
use usercenter\components\exception\Exception;

class MeicanApi
{

    const API_ADDMEMBER = '/v1/corps/:corp_prefix/addmember';
    const API_DELMEMBER = '/v1/corps/:corp_prefix/deletemember';
    const API_LISTMEMBER = '/v1/corps/:corp_prefix/listmember';
    const API_LISTBILL = '/v1/corps/:corp_prefix/listgrouporderandbill';
    const API_LISTRULE = '/v1/corps/:corp_prefix/listsubsidy';

    public static function genEmailUserId($userId){
        return str_pad(strval($userId),11,'0',STR_PAD_LEFT);
    }

    public static function genLoginUrl($userId){
        if(
            empty(\Yii::$app->params['meican_login'])
            || empty(\Yii::$app->params['meican_corp_prefix'])
            || empty(\Yii::$app->params['meican_email'])
            || empty(\Yii::$app->params['meican_crop_token'])
        ){
            throw new Exception("配置参数错误",Exception::ERROR_COMMON);
        }
        $loginUrl = \Yii::$app->params['meican_login'];
        $namespace = \Yii::$app->params['meican_corp_prefix'];
        $email = self::genEmailUserId($userId).\Yii::$app->params['meican_email'];
        $timestamp = intval(1000 * microtime(true));
        $sign = sha1(\Yii::$app->params['meican_crop_token'].$timestamp.$email,false);
        $url = "{$loginUrl}?namespace={$namespace}&email={$email}&version=1.1&timestamp={$timestamp}&signature={$sign}";
        return $url;
    }


    public static function curlApi($urlPath,$data = []){
        if(empty(\Yii::$app->params['meican_corp_prefix'])){
            return [];
        }
        $urlPath = str_replace(':corp_prefix',\Yii::$app->params['meican_corp_prefix'],$urlPath);
        $apiUrl = \Yii::$app->params['meican_api'];
        $timestamp = intval(1000 * microtime(true));
        $sign = sha1(\Yii::$app->params['meican_crop_token'].$timestamp,false);
        $data['timestamp'] = $timestamp;
        $data['signature'] = $sign;
        $retStr = AppFunc::postJson($apiUrl.$urlPath,$data);
        $retJson = json_decode($retStr,true);
        if(empty($retJson) || $retJson['resultCode'] != 'OK'){
            throw new Exception("美团请求失败".($retJson['resultDescription'] ?? ""));
        }
        return $retJson;
    }

    public static function addMember($userId){
        $email = self::genEmailUserId($userId);
        $ret = self::curlApi(self::API_ADDMEMBER,[
            'email'=>$email
        ]);
        return $ret;
    }

    public static function delMember($userId){
        $email = self::genEmailUserId($userId);
        $ret = self::curlApi(self::API_DELMEMBER,[
            'email'=>$email,
        ]);
        return $ret;
    }

    public static function listMember(){
        $ret = self::curlApi(self::API_LISTMEMBER);
        return $ret['data']['memberList'];
    }

    public static function listBill($day){
        $data = [];
        !empty($day) && $data['target'] = $day;
        $ret = self::curlApi(self::API_LISTBILL,$data);
        return $ret;
    }

    public static function listRule(){
        $ret = self::curlApi(self::API_LISTRULE);
        return $ret;
    }
}
