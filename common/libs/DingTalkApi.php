<?php
namespace common\libs;

use common\models\DingtalkUser;
use usercenter\components\exception\Exception;

class DingTalkApi {

    const APPSECRET='9YdKgU0RDEBznfl3KJHY_DXcLmXVgH8o6XFJusBL8Hn_7sMVjqmucu6yOIxKG5cD';
    const APPKEY='ding4isajaqgcgop8uuw';
    const APP_AGENT_ID = "241646599";
    const APPSECRET_MEICAN = 'PK4CNsCl0lby7JiztCiEDIcRlzM3R0KnQ5yrtsUa4u59fmCQS7Pejgh1TVwgM5gJ';
    const APPKEY_MEICAN = 'dingtnt2colo1xdamw1h';


    const APPKEY_ZHUZHENGLONG = 'dingy0y40sral9zvgrca';
    const APPSECRET_ZHUZHENGLONG = 'oNrdT_KEUif8xfEbE7iJTOzR-swFHtC53tzBtQsSGYydeGDWKWDoUyc8iF3e4OWl';


    const API_GETTOKEN = 'https://oapi.dingtalk.com/gettoken';//获取token
    const API_DEPARTMENT_LIST = 'https://oapi.dingtalk.com/department/list';//获取子部门ID列表
    const API_USER_GET = 'https://oapi.dingtalk.com/user/get';//获取用户信息
    const API_USER_GETDEPTMEMBER = 'https://oapi.dingtalk.com/user/getDeptMember';//获取部门用户
    const API_USER_GETDEPTMEMBER_USERINFO = 'https://oapi.dingtalk.com/user/listbypage';//获取部门用户信息
    const API_DEPARTMENT_PARENTLIST = 'https://oapi.dingtalk.com/department/list_parent_depts_by_dept';//获取父级IDList
    const API_CALLBACK_QUERY = 'https://oapi.dingtalk.com/call_back/get_call_back';//查询回调
    const API_GETUSERINFO_BYCODE = 'https://oapi.dingtalk.com/user/getuserinfo';//code换取userinfo
    //hrm
    const API_GET_USERINFO_BY_UIDS = 'https://oapi.dingtalk.com/topapi/smartwork/hrm/employee/list';//花名册
    const API_HRM_LIZHI_UIDS = 'https://oapi.dingtalk.com/topapi/smartwork/hrm/employee/querydimission';//分页查询企业离职员工userid列表
    const API_HRM_LIZHI_INFOS = 'https://oapi.dingtalk.com/topapi/smartwork/hrm/employee/listdimission';//分页查询企业离职员工信息

    const API_POST_UPDATE_EMAIL_BY_UID = "https://oapi.dingtalk.com/user/update"; //更新用户信息
    const API_SEND_WORK_MESSAGE = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2";

    const API_GET_ORG_USER_COUNT = "https://oapi.dingtalk.com/user/get_org_user_count";//获取企业员工人数
    const API_DEPARTMENT_LIST_IDS = "https://oapi.dingtalk.com/department/list_ids";//获取子部门ID列表
    const API_GET_DEPARTMENT_INFO = "https://oapi.dingtalk.com/department/get";//获取部门详情

    //注册业务事件回调接口
    const API_POST_REGISTER_CALL_BACK = "https://oapi.dingtalk.com/call_back/register_call_back";
    //审批
    const API_CHECK_PROCESS_LIST = "https://oapi.dingtalk.com/topapi/process/listbyuserid";//可见审批末班
    const API_TOPAPI_PROCESSINSTANCE_GET = "https://oapi.dingtalk.com/topapi/processinstance/get";//根据审批实例id调用此接口获取审批实例详情

    //考勤
    const API_TOPAPI_ATTENDANCE_LISTSCHEDULE = "https://oapi.dingtalk.com/topapi/attendance/listschedule";//企业考勤排班详情
    const API_TOPAPI_ATTENDANCE_LISTRECORD = "https://oapi.dingtalk.com/attendance/listRecord";//考勤打卡记录
    const API_TOPAPI_ATTENDANCE_LIST = "https://oapi.dingtalk.com/attendance/list";//考勤记录

    public static function getHrmLizhiUids(){
        $offset = 0;
        $list = [];
        while(1){
            $retJson = self::curlPost(self::API_HRM_LIZHI_UIDS,['size'=>50,'offset'=>$offset]);
            $list = array_merge($list,$retJson['result']['data_list']);
            if(empty($retJson['result']['next_cursor'])){
                break;
            }
            $offset  = $retJson['result']['next_cursor'];
            sleep(1);
        }
        return $list;
    }

    public static function getHrmLizhiUserInfo($userIdsAll){
        $list = [];
        $userIdsList = array_chunk($userIdsAll,50);
        foreach ($userIdsList as $userIds){
            $retJson = self::curlPost(self::API_HRM_LIZHI_INFOS,['userid_list'=>join(',',$userIds)]);
            $list = array_merge($list,$retJson['result']);
        }
        return $list;
    }




    public static function getCheckProcessList(){
        $offset = 0;
        $list = [];
        while(1){
            $retJson = self::curlPost(self::API_CHECK_PROCESS_LIST,['size'=>100,'offset'=>$offset]);
            $list = array_merge($list,$retJson['result']['process_list']);
            if(empty($retJson['result']['next_cursor'])){
                break;
            }
            $offset  = $retJson['result']['next_cursor'];
        }
        return $list;
    }


    public static function getDepartmentUserInfoList($departmentId){
        $offset = 0;
        $userInfoList = [];
        while(1){
            $retJson = self::curlGet(self::API_USER_GETDEPTMEMBER_USERINFO,['size'=>100,'offset'=>$offset,'department_id'=>$departmentId]);
            $userInfoList = array_merge($userInfoList,$retJson['userlist']);
            if(!$retJson['hasMore']){
                break;
            }
            $offset += 100;
        }
        return $userInfoList;
    }


    public static function getAttendanceListSchedule($date){
        $offset = 0;
        $scheduleList = [];
        while(1){
            /**
            {
            "result":{
            "schedules":[
            {
            "plan_id":1,
            "check_type":"OnDuty",
            "approve_id":1,
            "userid":"0001",
            "class_id":1,
            "class_setting_id":1,
            "plan_check_time":"2017-04-11 11:11:11",
            "group_id":1
            }
            ],
            "has_more":false
            },
            "errmsg":"ok",
            "errcode":0
            }
             */
            $retJson = self::curlPost(self::API_TOPAPI_ATTENDANCE_LISTSCHEDULE,['size'=>200,'offset'=>$offset,'workDate'=>$date]);
            $scheduleList = array_merge($scheduleList,$retJson['result']['schedules']);
            if(!$retJson['result']['has_more']){
                break;
            }
            $offset += 200;
        }
        return $scheduleList;
    }

    public static function getAttendanceListRecord($startTime,$endTime,$userIds){

        $list = [];
        $userIdsChunkList = array_chunk($userIds,50);
        foreach ($userIdsChunkList as $userIdsChunk){
            //补卡算 班次时间范围  不算在补卡时间范围内
            $retJson = self::curlPost(self::API_TOPAPI_ATTENDANCE_LISTRECORD,[
                'userIds'=>$userIdsChunk,
                'checkDateFrom'=>$startTime,
                'checkDateTo'=>$endTime,
                'isI18n'=>"false",
            ]);
            $list = array_merge($list,$retJson['recordresult']);
        }
        return $list;
    }

    public static function getAttendanceList($startTime,$endTime,$userIds){
        $list = [];
        $userIdsChunkList = array_chunk($userIds,50);

        foreach ($userIdsChunkList as $userIdsChunk){
            $offset = 0;
            while(1){
                //补卡算 班次时间范围  不算在补卡时间范围内
                $retJson = self::curlPost(self::API_TOPAPI_ATTENDANCE_LIST,[
                    'userIdList'=>$userIdsChunk,
                    'workDateFrom'=>$startTime,
                    'workDateTo'=>$endTime,
                    'isI18n'=>"false",
                    'offset'=>$offset,
                    'limit'=>50
                ]);
                $list = array_merge($list,$retJson['recordresult']);
                if(empty($retJson['hasMore'])){
                    break;
                }
                $offset += 50;
            }
        }

        return $list;

    }






    /**
     * onlyActive
     * 0：包含未激活钉钉的人员数量
     * 1：不包含未激活钉钉的人员数量
     */
    public static function getOrgUserCount($onlyActive=1){
        $params = [
            'onlyActive'=>$onlyActive
        ];
        $retJson = self::curlGet(self::API_GET_ORG_USER_COUNT,$params);
        return $retJson['count'];
    }


    /**
     * id
     * 父部门id。根部门的话传1
     */
    public static function departmentListIds($parentDepartmentId=1){
        $params = [
            'id'=>$parentDepartmentId
        ];
        $retJson = self::curlGet(self::API_DEPARTMENT_LIST_IDS,$params);
        return $retJson['sub_dept_id_list'];
    }


    /**
     * id
     * 部门id
     */
    public static function getDepartmentInfo($departmentId=1){
        $params = [
            'id'=>$departmentId
        ];
        $retJson = self::curlGet(self::API_GET_DEPARTMENT_INFO,$params);
        return $retJson;
    }


    public static function getDingDbInfoByCode($code,$type='meican'){
        if($type == 'meican'){
            $token = self::getAccessTokenMeican();
        }elseif($type == 'zzl'){
            $token = self::getAccessTokenZzl();
        }else{
            throw new Exception("不支持的类型");
        }
        $url = self::API_GETUSERINFO_BYCODE.'?access_token='.$token.'&code='.$code;
        $retStr = AppFunc::curlGet($url);
        $retJson = json_decode($retStr,true);
        if(!isset($retJson['errcode']) || 0 != $retJson['errcode']){
            throw new Exception('[DINGMM]'.$retJson['errmsg']??"");
        }
        $userInfo = DingtalkUser::findOneByWhere(['user_id'=>$retJson['userid']]);
        if(empty($userInfo) || empty($userInfo['kael_id'])){
            throw new Exception("用户信息不存在");
        }
        return $userInfo;
    }




    public static function getUserInfoByCode($code,$type='meican'){
        if($type == 'meican'){
            $token = self::getAccessTokenMeican();
        }elseif($type == 'zzl'){
            $token = self::getAccessTokenZzl();
        }else{
            throw new Exception("不支持的类型");
        }
        $url = self::API_GETUSERINFO_BYCODE.'?access_token='.$token.'&code='.$code;
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

    public static function getHrmUserInfoByUids($uids){
        $params = [
            'userid_list'=>is_array($uids)?implode(',',$uids):$uids,
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
        if (\Yii::$app->params['env'] != 'prod') {
            return false;
        }
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

    private static function getAccessTokenZzl(){
        $key = 'DINGTALK_ACCESS_TOKEN_'.self::APPKEY_ZHUZHENGLONG;
        $ret = Cache::getCacheString($key);
        if(!empty($ret)){
            return $ret;
        }
        $retStr = AppFunc::curlGet(self::API_GETTOKEN.'?appkey='.self::APPKEY_ZHUZHENGLONG.'&appsecret='.self::APPSECRET_ZHUZHENGLONG);
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
        $retStr = AppFunc::postJson($url,$data);
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

    public static function sendWorkMessage($msgType='text',$data=[],$userIds='',$departIds='',$allUser=false){
        $params = [];
        $params['agent_id'] = self::APP_AGENT_ID;
        !empty($userIds) && $params['userid_list'] = $userIds;
        !empty($departIds) && $params['dept_id_list'] = $departIds;
        $allUser && $params['to_all_user'] = $allUser;
        $message = [];
        $message['msgtype'] = $msgType;
        switch ($msgType){
            case "text":
                $message[$msgType]['content'] = $data['content']??'';
                break;
            case "image":
            case "file":
                $message[$msgType]['media_id'] = $data['media_id']??'';
                break;
            case "voice":
                $message[$msgType]['media_id'] = $data['media_id']??'';
                $message[$msgType]['duration'] = $data['duration']??'';
                break;
            case "link":
                $message[$msgType]['messageUrl'] = $data['messageUrl']??'';
                $message[$msgType]['picUrl'] = $data['picUrl']??'';
                $message[$msgType]['title'] = $data['title']??'';
                $message[$msgType]['text'] = $data['text']??'';
                break;
            case "oa":
                $message[$msgType]['message_url'] = $data['message_url']??'';
                $message[$msgType]['head'] = $data['head']??'';
                $message[$msgType]['body'] = $data['body']??'';
                break;
            case "markdown":
                $message[$msgType]['title'] = $data['title']??'';
                $message[$msgType]['text'] = $data['text']??'';
                break;
            case "action_card":
                $message[$msgType] = $data;
                break;
            default :
                throw new Exception("消息错误,无此类型消息",Exception::ERROR_COMMON);
        }
//        is_array($message) && $message = json_encode($message,true);
        $params['msg'] = $message;
        $info = self::curlPost(self::API_SEND_WORK_MESSAGE,$params);
        try{
            if($info['errcode']){
                throw new Exception($info['errmsg'],Exception::ERROR_COMMON);
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }
        return $info;
    }


    public static function registerCallBack($data){
        if (\Yii::$app->params['env'] != 'prod') {
            return false;
        }
        $retJson = self::curlPost(self::API_POST_REGISTER_CALL_BACK,$data);
        return $retJson;
    }

    public static function getProcessInstance($proc_inst_id){

        $retJson = self::curlPost(self::API_TOPAPI_PROCESSINSTANCE_GET,['process_instance_id'=>$proc_inst_id]);
        return $retJson;
    }





}
