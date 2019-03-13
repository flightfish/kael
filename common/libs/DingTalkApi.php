<?php
namespace common\libs;

use usercenter\components\exception\Exception;

class DingTalkApi {

    const APPSECRET='9YdKgU0RDEBznfl3KJHY_DXcLmXVgH8o6XFJusBL8Hn_7sMVjqmucu6yOIxKG5cD';
    const APPKEY='ding4isajaqgcgop8uuw';

    const API_GETTOKEN = 'https://oapi.dingtalk.com/gettoken';//获取token
    const API_DEPARTMENT_LIST = 'https://oapi.dingtalk.com/department/list';//获取子部门ID列表
    const API_USER_GET = 'https://oapi.dingtalk.com/user/get';//获取用户信息
    const API_USER_GETDEPTMEMBER = 'https://oapi.dingtalk.com/user/getDeptMember';//获取部门用户
    const API_DEPARTMENT_PARENTLIST = 'https://oapi.dingtalk.com/department/list_parent_depts_by_dept';//获取父级IDList
    const API_CALLBACK_QUERY = 'https://oapi.dingtalk.com/call_back/get_call_back';//查询回调

    const API_GETUSERINFO_BYCODE = 'https://oapi.dingtalk.com/user/getuserinfo?access_token=access_token&code=code';//code换取userinfo

    public static function getUserIdByCode($code){
        $retJson = self::curlGet(self::API_GETUSERINFO_BYCODE,['code'=>$code]);
        return $retJson['userid'];
    }

    public static function callBackQuery(){
        $retJson = self::curlGet(self::API_CALLBACK_QUERY);
        return $retJson;
    }

    public static function getDepartmentUserIds($departmentId){
        $retJson = self::curlGet(self::API_USER_GETDEPTMEMBER,['deptId'=>$departmentId]);
        return $retJson['userIds'];
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



    private static function getAccessToken(){
        $key = 'DINGTALK_ACCESS_TOKEN_'.self::APPKEY;
        $ret = Cache::getCacheString($key);
        if(!empty($ret)){
            return $ret;
        }
        $retStr = AppFunc::curlGet(self::API_GETTOKEN.'?appkey='.self::APPKEY.'&appsecret='.self::APPSECRET);
        $retJson = json_decode($retStr,true);
        /**
         {
        "errcode": 0,
        "errmsg": "ok",
        "access_token": "fw8ef8we8f76e6f7s8df8s"
        }
         */
        if(empty($retJson) || empty($retJson['access_token'])){
            throw new Exception("[DING]".$retJson['errmsg'] ?? $retStr);
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


}
