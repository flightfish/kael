<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class AliMailApi{

    const ALIMAIL_API_HOST = "https://alimailws.alibaba-inc.com/alimailws";

    const API_GETTOKEN = "/control/wsLogin";
    const API_DEPART_CREATE = "/ud/createDepartment";
    const API_DEPART_UPDATE = "/ud/updateDepartment";
    const API_DEPART_REMOVE = "/ud/removeDepartment";
    const API_DEPART_LIST = "/ud/getDepartmentList";
    const API_USER_UPDATE_DEPART = "/ud/moveAccountsToDepartment";
    const API_USER_CREATE = "/ud/createAccounts";
    const API_USER_REMOVE = "/ud/removeAccounts";
    const API_USER_UPDATE = "/ud/updateAccountInfo";
    const API_USER_INFO = "/ud/getAccountsInfo";

    private static $token = [];


    private static function curlApi($path,$data,$fingerPrint="",$deep=0){
        if($deep >= 2){
            throw new Exception("重试次数过多",Exception::ERROR_COMMON);
        }
        if(empty($fingerPrint)){
            $fingerPrint = microtime(true).uniqid();
        }
        $cacheKey = 'ALIMAIL_ACCESSTOKEN_'.(\Yii::$app->params['alimail_accessCode']);
        if(empty(self::$token[\Yii::$app->params['alimail_accessCode']])){
            self::$token[\Yii::$app->params['alimail_accessCode']] = Cache::getCacheString($cacheKey);
            if(empty(self::$token[\Yii::$app->params['alimail_accessCode']])){
                self::getAccessToken();
            }
        }

        $url = self::ALIMAIL_API_HOST.$path;
        $body = [
            "access"=>[
                "accessToken"=>self::$token[\Yii::$app->params['alimail_accessCode']],
                "accessTarget"=>'knowbox.cn',
//                "fingerPrint"=>$fingerPrint
            ],
            "param"=>$data
        ];
        $postString = json_encode($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=UTF-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $retStr = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($retStr,true);
        if(empty($json) || empty($json['status']) || $json['status']['statusCode'] != 100){
            echo $url."\n".$postString."\n".$retStr."\n\n";
            if($json['status']['statusCode'] == 408){
                self::getAccessToken();
                self::curlApi($path,$data,$fingerPrint,$deep+1);
            }else{
                throw new Exception("[ALIMAIL]".$json);
            }

        }
        return $json['data'];
    }

    private static function curlApiNoToken($path,$data,$fingerPrint=""){
        if(empty($fingerPrint)){
            $fingerPrint = microtime(true).uniqid();
        }
        $url = self::ALIMAIL_API_HOST.$path;
        $body = [
            "access"=>[
                "accessToken"=>'',
                "accessTarget"=>'knowbox.cn',
//                "fingerPrint"=>$fingerPrint
            ],
            "param"=>$data
        ];
        $postString = json_encode($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=UTF-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $retStr = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($retStr,true);
        if(empty($json) || empty($json['status']) || $json['status']['statusCode'] != 100){
            throw new Exception("[ALIMAIL]".$retStr);
        }
        return $json['data'];
    }


    //token
    public static function getAccessToken(){
        echo date("Y-m-d H:i:s")." get access token\n";
        /**
        accesscode:knowboxcnws
        password:knowBox357#123alimail
         */
        $res = self::curlApiNoToken(self::API_GETTOKEN,[
            'accessCode'=>\Yii::$app->params['alimail_accessCode'],
            'accessPassword'=>\Yii::$app->params['alimail_accessPassword']
        ]);
        $cacheKey = 'ALIMAIL_ACCESSTOKEN_'.(\Yii::$app->params['alimail_accessCode']);
        Cache::setCacheString($cacheKey,24 * 3600 -30 ,$res['accessToken']);
        self::$token[\Yii::$app->params['alimail_accessCode']] = $res['accessToken'];
        return $res['accessToken'];
    }

    //department
    public static function createDepartment($departmentId,$name){
        return self::curlApi(self::API_DEPART_CREATE,[
            'name'=>$name,
            'parentId'=>0,
            'customDepartmentId'=>$departmentId,
        ]);
    }
    public static function updateDeparmtment($departmentId,$name){
        return self::curlApi(self::API_DEPART_UPDATE,[
            'name'=>$name,
            'departmentId'=>$departmentId,
        ]);
    }
    public static function removeDepartment($departmentId){
        return self::curlApi(self::API_DEPART_REMOVE,[
            'departmentId'=>$departmentId,
        ]);
    }
    public static function departmentList(){
        $ret = self::curlApi(self::API_DEPART_LIST,[
        ]);
        /**
        {
        "accountNum":"xx",
        "createdTime":"xx",
        "departmentId":"xx",
        "lastModifiedTime":"xx",
        "name":"xx",
        "parentId":"xx"
        }
         */
        return $ret['dataList'];
    }
    //user
    public static function updateUserDepartment($toDepartmentId,$email){
        $ret = self::curlApi(self::API_USER_UPDATE_DEPART,[
            "toDepartmentId"=>$toDepartmentId,
            "emails"=>[$email]
        ]);
        if(!empty($ret['fail'])){
            throw new Exception(json_encode($ret['fail']),Exception::ERROR_COMMON);
        }
        return $ret['success'];
    }
    public static function createUser($name,$email,$departmentId){
        $ret = self::curlApi(self::API_USER_CREATE,[
            "accounts"=>[
                "name"=>$name,
                "passwd"=>'1Knowbox!',
                "email"=>$email,
                "departmentId"=>$departmentId
            ]
        ]);
        if(!empty($ret['fail'])){
            throw new Exception(json_encode($ret['fail']),Exception::ERROR_COMMON);
        }
        return $ret;
    }
    public static function userDel($email){
        $ret = self::curlApi(self::API_USER_REMOVE,[
            "emails"=>[$email],
        ]);
        if(!empty($ret['fail'])){
            throw new Exception(json_encode($ret['fail']),Exception::ERROR_COMMON);
        }
        return $ret;
    }
    public static function userInfoList($email,$fields=[]){
        return self::curlApi(self::API_USER_REMOVE,[
            "emails"=>[$email],
            "fields"=>$fields,//name....
        ]);
    }
}
