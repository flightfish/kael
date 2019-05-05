<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class DingTalkApi {

    const APPSECRET='9YdKgU0RDEBznfl3KJHY_DXcLmXVgH8o6XFJusBL8Hn_7sMVjqmucu6yOIxKG5cD';
    const APPKEY='ding4isajaqgcgop8uuw';

    const APPSECRET_MEICAN = 'PK4CNsCl0lby7JiztCiEDIcRlzM3R0KnQ5yrtsUa4u59fmCQS7Pejgh1TVwgM5gJ';
    const APPKEY_MEICAN = 'dingtnt2colo1xdamw1h';


    const API_GETTOKEN = 'https://oapi.dingtalk.com/gettoken';//获取token
    const API_DEPARTMENT_LIST = 'https://oapi.dingtalk.com/department/list';//获取子部门ID列表
    const API_USER_GET = 'https://oapi.dingtalk.com/user/get';//获取用户信息
    const API_USER_GETDEPTMEMBER = 'https://oapi.dingtalk.com/user/getDeptMember';//获取部门用户
    const API_DEPARTMENT_PARENTLIST = 'https://oapi.dingtalk.com/department/list_parent_depts_by_dept';//获取父级IDList
    const API_CALLBACK_QUERY = 'https://oapi.dingtalk.com/call_back/get_call_back';//查询回调

    const API_GETUSERINFO_BYCODE = 'https://oapi.dingtalk.com/user/getuserinfo';//code换取userinfo

    const API_GET_USERINFO_BY_UIDS = 'https://oapi.dingtalk.com/topapi/smartwork/hrm/employee/list';

    const API_POST_UPDATE_EMAIL_BY_UID = "https://oapi.dingtalk.com/user/update";


    public static function getUserInfoByCode($code){
        $url = self::API_GETUSERINFO_BYCODE.'?access_token='.self::getAccessTokenMeican().'&code='.$code;
        $retStr = AppFunc::curlGet($url);
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[DINGMM]'.$retJson['errmsg']??"");
        }
        $retJson = self::getUserInfo($retJson['userid']);
        return $retJson;
    }

    public static function callBackQuery(){
        $retJson = self::curlGet(self::API_CALLBACK_QUERY);
        return $retJson;
    }

    public static function getDepartmentUserIds($departmentId){
        $retJson = self::curlGet(self::API_USER_GETDEPTMEMBER,['deptId'=>$departmentId]);
        return $retJson['userIds'];
    }

    //通过智能人事的接口获取用户的详细信息
    public static function getUserInfoForFieldsByUids($uids,$fields){
        $params = [
            'userid_list'=>is_array($uids)?implode(',',$uids):$uids,
            'field_filter_list'=>is_array($fields)?implode(',',$fields):$fields
        ];
        $response = self::curlGet(self::API_GET_USERINFO_BY_UIDS,$params);
        return $response['result'];
    }

    public static function getUserInfo($userId){
        $retJson = self::curlGet(self::API_USER_GET,['userid'=>$userId]);
        return $retJson;
    }

    /**
     * @param $parentId integer 默认为1 根开始全部
     * @return array
     */
    public static function getDepartmentAllList($parentId=1){
        $retJson = self::curlGet(self::API_DEPARTMENT_LIST,['id'=>$parentId]);
        return $retJson['department'];
    }

    public static function getDepartmentParentList($departmentId){
        $retJson = self::curlGet(self::API_DEPARTMENT_PARENTLIST,['id'=>$departmentId]);
        return $retJson['parentIds'];
    }

    public static function updateEmailForUser($userId,$email){
        $params = [
            'userid'=>$userId,
            'email'=>$email
        ];
        $retJson = self::curlPost(self::API_POST_UPDATE_EMAIL_BY_UID,$params);
        return $retJson;
    }

    private static function getAccessToken(){
        $key = 'DINGTALK_ACCESS_TOKEN_'.self::APPKEY;
        $ret = Cache::getCacheString($key);
        if(!empty($ret)){
            return $ret;
        }
        $retStr = AppFunc::curlGet(self::API_GETTOKEN.'?appkey='.self::APPKEY.'&appsecret='.self::APPSECRET);
        $retJson = json_decode($retStr,true);
        if(empty($retJson) || empty($retJson['access_token'])){
            throw new Exception("[DING]".$retJson['errmsg'] ?? $retStr);
        }
        Cache::setCacheString($key,7000,$retJson['access_token']);
        return $retJson['access_token'];
    }

    private static function getAccessTokenMeican(){
        $key = 'DINGTALK_ACCESS_TOKEN_'.self::APPKEY_MEICAN;
        $ret = Cache::getCacheString($key);
        if(!empty($ret)){
            return $ret;
        }
        $retStr = AppFunc::curlGet(self::API_GETTOKEN.'?appkey='.self::APPKEY_MEICAN.'&appsecret='.self::APPSECRET_MEICAN);
        $retJson = json_decode($retStr,true);
        if(empty($retJson) || empty($retJson['access_token'])){
            throw new Exception("[DINGM]".$retJson['errmsg'] ?? $retStr);
        }
        Cache::setCacheString($key,7000,$retJson['access_token']);
        return $retJson['access_token'];
    }

    private static function curlGet($url,$data=[]){
        $url .= '?access_token='.self::getAccessToken();
        $dataStr = http_build_query($data);
        !empty($dataStr) && $url = $url.'&'.$dataStr;
        $retStr = AppFunc::curlGet($url);
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[DING]'.$retJson['errmsg']??"");
        }
        return $retJson;
    }
    private static function curlPost($url,$data=[],$header=[]){
        $url .= '?access_token='.self::getAccessToken();
        $retStr = AppFunc::curlPost($url,$data,$header);
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[DING]'.$retJson['errmsg']??"");
        }
        return $retJson;
    }

    private static function curlGetMeican($url,$data=[]){
        $url .= '?access_token='.self::getAccessTokenMeican();
        $dataStr = http_build_query($data);
        !empty($dataStr) && $url = $url.'&'.$dataStr;
        $retStr = AppFunc::curlGet($url);
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[DING]'.$retJson['errmsg']??"");
        }
        return $retJson;
    }




}
