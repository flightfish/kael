<?php

namespace usercenter\modules\meican\models;



use common\libs\AppFunc;
use usercenter\components\exception\Exception;

class ZzlApi
{


    const API_LOGINURL = '/site/zyhz';
    const API_ORDERLIST = '/order/list';

    public static function genUserId($userId){
        return 'Z'.str_pad(strval($userId),10,'0',STR_PAD_LEFT);
    }

    public static function genLoginUrl($userId,$userName,$departmentName){
        if(
            empty(\Yii::$app->params['zzl_secretkey'])
            || empty(\Yii::$app->params['zzl_url'])
        ){
            throw new Exception("配置参数错误",Exception::ERROR_COMMON);
        }
        $uid = self::genUserId($userId);
        $url = \Yii::$app->params['zzl_url']
            .self::API_LOGINURL
            ."?userid={$uid}&department_name={$departmentName}&username={$userName}&secretkey=".\Yii::$app->params['zzl_secretkey'];
        return $url;
    }


    public static function curlApi($urlPath,$data = []){
        if(
            empty(\Yii::$app->params['zzl_secretkey'])
            || empty(\Yii::$app->params['zzl_url'])
        ){
            throw new Exception("配置参数错误",Exception::ERROR_COMMON);
        }
        echo  'ssssss';return ;
        $data['secretkey'] = \Yii::$app->params['zzl_secretkey'];
        $apiUrl = \Yii::$app->params['zzl_url'].$urlPath;
        $retStr = AppFunc::curlPost($apiUrl,$data);

        $retJson = json_decode($retStr,true);
        if(empty($retJson) || $retJson['resultCode'] != 'OK'){
            throw new Exception("竹蒸笼请求失败".($retStr));
        }
        return $retJson;
    }

    public static function orderList($day)
    {
        $data = [];
        if(!empty($day)){
            $data['start_date'] = date("Y-m-d",strtotime($day));
            $data['end_date'] = date("Y-m-d",strtotime($day));
        }
        return self::curlApi(self::API_ORDERLIST,$data);
    }
}
