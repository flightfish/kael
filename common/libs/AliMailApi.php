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


    private static function curlApi($path,$data,$fingerPrint=""){
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
            "params"=>$data
        ];
        $postString = json_encode($body);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($data,true);
        if(empty($data) || empty($data['status']) || $data['status']['statusCode'] != 100){
            echo $url."\n".$postString."\n".$data."\n\n";
            throw new Exception("[ALIMAIL]".$data);
        }
        return $json['data'];
    }

    //token
    public static function getAccessToken(){
        /**
        accesscode:knowboxcnws
        password:knowBox357#123alimail
         */
        $res = self::curlApi(self::API_GETTOKEN,[
            'accessCode'=>\Yii::$app->params['alimail_accessCode'],
            'accessPassword'=>\Yii::$app->params['alimail_accessPassword']
        ]);
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
