<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkAttendanceProcessInstance;
use common\models\DingtalkAttendanceRecord;
use common\models\DingtalkAttendanceResult;
use common\models\DingtalkAttendanceSchedule;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use yii\console\Controller;

class DingKaoqinController extends Controller
{

    /**
     * 钉钉考勤信息初始化 ding-kaoqin/init
     */
    public function actionInit(){
        if(exec('ps -ef|grep "ding-kaoqin/init"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        echo date('Y-m-d H:i:s')."\t组装用户ID\n";
//        $userIds = array_values(array_filter(array_unique(array_column(DingtalkUser::findList([],'','user_id',-1),'user_id'))));
        //dayList
        $dayList = array_map(function($v){
            return date("Y-m-d",$v);
        },range(strtotime('2019-07-01'),time(),24*3600));
        //钉钉信息
        $userIdToDepartmentId = array_column(DingtalkUser::findList([],'','user_id,department_id',-1),'department_id','user_id');
        $departmentIdToInfo = array_column(DingtalkDepartment::findList([],'','id,name,subroot_id',-1),null,'id');
        $departmentIdToInfo[1] = ['id'=>1,'name'=>'小盒科技','subroot_id'=>1];
        $userIds = array_keys($userIdToDepartmentId);
        $userIdToDepartmentInfo = [];
        foreach ($userIdToDepartmentId as $userId => $departmentId){
            $departmentName = '';
            $subrootId = 0;
            $subrootName = '';
            $departmentInfo = $departmentIdToInfo[$departmentId] ?? [];
            if(!empty($departmentInfo)){
                $departmentName = $departmentInfo['name'];
                $subrootId = $departmentInfo['subroot_id'];
                $subrootName = ($departmentIdToInfo[$subrootId] ?? [])['name'] ?? '';
            }
            $userIdToDepartmentInfo[$userId] = [
                'department_id'=>$departmentId,
                'department_name'=>$departmentName,
                'subroot_id'=>$subrootId,
                'subroot_name'=>$subrootName,
            ];
        }

        foreach ($dayList as $day){
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步排班时间数据到kael\n";
            $this->synSchedule($day);
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步考勤数据到kael\n";
            $this->synKaoqin($day,$userIds);
            echo date("Y-m-d H:i:s")."\t {$day} 开始同步考勤数据详情到kael\n";
            $this->synKaoqinRecord($day,$userIds);
            echo date('Y-m-d H:i:s')."\t {$day} 同步数据结束\n";
        }
        echo date('Y-m-d H:i:s')."\t 同步部门数据\n";
        foreach ($userIdToDepartmentInfo as $userId=>$departmentInfo){
            DingtalkAttendanceSchedule::updateAll([
                'dingtalk_department_id'=>$departmentInfo['department_id'],
                'dingtalk_department_name'=>$departmentInfo['department_name'],
                'dingtalk_subroot_id'=>$departmentInfo['subroot_id'],
                'dingtalk_subroot_name'=>$departmentInfo['subroot_name'],
            ],['user_id'=>$userId,'dingtalk_department_id'=>0]);
            DingtalkAttendanceResult::updateAll([
                'dingtalk_department_id'=>$departmentInfo['department_id'],
                'dingtalk_department_name'=>$departmentInfo['department_name'],
                'dingtalk_subroot_id'=>$departmentInfo['subroot_id'],
                'dingtalk_subroot_name'=>$departmentInfo['subroot_name'],
            ],['user_id'=>$userId,'dingtalk_department_id'=>0]);
        }
        echo date('Y-m-d H:i:s')."\t 同步部门数据结束\n";
    }

    /**
     * 钉钉考勤信息同步 ding-kaoqin/syn
     */
    public function actionSyn(){
        if(exec('ps -ef|grep "ding-kaoqin/syn"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        echo date('Y-m-d H:i:s')."\t组装用户ID\n";
//        $userIds = array_values(array_filter(array_unique(array_column(DingtalkUser::findList([],'','user_id'),'user_id'))));
        //dayList
        $dayList = array_map(function($v){
            return date("Y-m-d",$v);
        },range(time()-7*24*3600,time(),24*3600));
        //钉钉信息
        $userIdToDepartmentId = array_column(DingtalkUser::findList([],'','user_id,department_id',-1),'department_id','user_id');
        $departmentIdToInfo = array_column(DingtalkDepartment::findList([],'','id,name,subroot_id',-1),null,'id');
        $departmentIdToInfo[1] = ['id'=>1,'name'=>'小盒科技','subroot_id'=>1];
        $userIds = array_keys($userIdToDepartmentId);
        $userIdToDepartmentInfo = [];
        foreach ($userIdToDepartmentId as $userId => $departmentId){
            $departmentName = '';
            $subrootId = 0;
            $subrootName = '';
            $departmentInfo = $departmentIdToInfo[$departmentId] ?? [];
            if(!empty($departmentInfo)){
                $departmentName = $departmentInfo['name'];
                $subrootId = $departmentInfo['subroot_id'];
                $subrootName = ($departmentIdToInfo[$subrootId] ?? [])['name'] ?? '';
            }
            $userIdToDepartmentInfo[$userId] = [
                'department_id'=>$departmentId,
                'department_name'=>$departmentName,
                'subroot_id'=>$subrootId,
                'subroot_name'=>$subrootName,
            ];
        }
        foreach ($dayList as $day){
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步排班时间数据到kael\n";
            $this->synSchedule($day);
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步考勤数据到kael\n";
            $this->synKaoqin($day,$userIds);
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步考勤记录到kael\n";
            $this->synKaoqinRecord($day,$userIds);
            echo date('Y-m-d H:i:s')."\t {$day} 同步考勤数据结束\n";
        }
        echo date('Y-m-d H:i:s')."\t 同步部门数据\n";
        foreach ($userIdToDepartmentInfo as $userId=>$departmentInfo){
            DingtalkAttendanceSchedule::updateAll([
                'dingtalk_department_id'=>$departmentInfo['department_id'],
                'dingtalk_department_name'=>$departmentInfo['department_name'],
                'dingtalk_subroot_id'=>$departmentInfo['subroot_id'],
                'dingtalk_subroot_name'=>$departmentInfo['subroot_name'],
            ],['user_id'=>$userId,'dingtalk_department_id'=>0]);
            DingtalkAttendanceResult::updateAll([
                'dingtalk_department_id'=>$departmentInfo['department_id'],
                'dingtalk_department_name'=>$departmentInfo['department_name'],
                'dingtalk_subroot_id'=>$departmentInfo['subroot_id'],
                'dingtalk_subroot_name'=>$departmentInfo['subroot_name'],
            ],['user_id'=>$userId,'dingtalk_department_id'=>0]);
        }
        echo date('Y-m-d H:i:s')."\t 同步部门数据结束\n";

    }

    public function synSchedule($day){
        //排班时间
        $scheduleList = DingTalkApi::getAttendanceListSchedule(date('Y-m-d',strtotime($day)));
        $columns = [];
        $rows = [];
        foreach ($scheduleList as $v){
            /**
            {
            "check_type": "OnDuty",
            "class_id": 80240005,
            "class_setting_id": 59495148,
            "group_id": 160790019,
            "plan_check_time": "2019-09-16 10:00:00",
            "plan_id": 72491442011,
            "userid": "00003"
            }
             */
            if(empty($v['plan_check_time'])){
                continue;
            }
            try{
                $tmp = [
                    'plan_id'=>$v['plan_id'],
                    'schedule_date'=>date("Y-m-d",strtotime($v['plan_check_time'])),
                    'check_type'=>$v['check_type'],
                    'approve_id'=>$v['approve_id'] ?? 0,
                    'user_id'=>$v['userid'],
                    'class_id'=>$v['class_id'] ?? 0,
                    'class_setting_id'=>$v['class_setting_id'] ?? 0,
                    'plan_check_time'=>$v['plan_check_time'],
                    'group_id'=>$v['group_id'],
                ];
            }catch (\Exception $e){
                echo json_encode($v,64|256)."\n";
                throw $e;
            }

            empty($columns) && $columns = array_keys($tmp);
            $rows[] = array_values($tmp);
        }
        DingtalkAttendanceSchedule::addUpdateColumnRows($columns,$rows);
    }

    public function synKaoqin($day,$userIds){
        $columns = [];
        $rows = [];
        //考勤时间
        $attendanceList = DingTalkApi::getAttendanceList(
            date('Y-m-d 00:00:00',strtotime($day)),
            date('Y-m-d 23:59:59',strtotime($day)),
            $userIds);
        foreach ($attendanceList as $v){
            /**
            "baseCheckTime": 1463392800000,
            "checkType": "OffDuty",
            "corpId": "ding53a2fb0458ba9639",
            "groupId": 20451893,
            "id": 60714703,
            "locationResult": "Normal",
            "planId": 210071562,
            "recordId": 30068312,
            "timeResult": "Early",
            "userCheckTime": 1463392235000,
            "userId": "manager6699",
            "workDate": 1463328000000,
            "procInstId": "cb992267-9b70"
             */
            isset($v['baseCheckTime']) && $v['baseCheckTime'] = date("Y-m-d H:i:s",intval($v['baseCheckTime']/1000));
            isset($v['workDate']) && $v['workDate'] = date("Y-m-d",intval($v['workDate']/1000));
            isset($v['userCheckTime']) && $v['userCheckTime'] = date("Y-m-d H:i:s",intval($v['userCheckTime']/1000));


            $tmp = [
                'id'=>$v['id'],
                'group_id'=>$v['groupId'] ?? 0,
                'plan_id'=>$v['planId'] ?? 0,
                'record_id'=>$v['recordId'] ?? 0,
                'work_date'=>$v['workDate']??'0000-00-00',
                'user_id'=>$v['userId'],
                'check_type'=>$v['checkType']??'',
                'time_result'=>$v['timeResult']??'',
                'location_result'=>$v['locationResult']??'',
                'approve_id'=>$v['approveId']??0,
                'proc_inst_id'=>$v['procInstId']??0,
                'base_check_time'=>$v['baseCheckTime']??'0000-00-00 00:00:00',
                'user_check_time'=>$v['userCheckTime']??'0000-00-00 00:00:00',
                'source_type'=>$v['sourceType'],
            ];
            empty($columns) && $columns = array_keys($tmp);
            $rows[] = array_values($tmp);
        }
        DingtalkAttendanceResult::addUpdateColumnRows($columns,$rows);
    }

    public function synKaoqinRecord($day,$userIds){
        $columns = [];
        $rows = [];
        //考勤时间
        $attendanceList = DingTalkApi::getAttendanceListRecord(
            date('Y-m-d 00:00:00',strtotime($day)),
            date('Y-m-d 23:59:59',strtotime($day)),
            $userIds);
        foreach ($attendanceList as $v){
            /**
            "isLegal": "N",
            "baseCheckTime": 1492568460000,
            "id": 933202551,
            "userAddress": "北京市朝阳区崔各庄镇阿里中心.望京A座阿里巴巴绿地中心",
            "userId": "manager7078",
            "checkType": "OnDuty",
            "timeResult": "Normal",
            "deviceId": "cb7ace07d52fe9be14f4d8bec5e1ba79",
            "corpId": "ding7536bfee6fb1fa5a35c2f4657eb6378f",
            "sourceType": "USER",
            "workDate": 1492531200000,
            "planCheckTime": 1492568497000,
            "locationMethod": "MAP",
            "locationResult": "Outside",
            "userLongitude": 116.486888,
            "planId": 4550269081,
            "groupId": 121325603,
            "userAccuracy": 65,
            "userCheckTime": 1492568497000,
            "userLatitude": 39.999946,
            "procInstId": "cb992267-9b70"
             */
            isset($v['workDate']) && $v['workDate'] = date("Y-m-d",intval($v['workDate']/1000));
            try{
                $tmp = [
                    'id'=>$v['id'],
                    'work_date'=>$v['workDate']??'0000-00-00',
                    'user_id'=>$v['userId'],
                    'check_type'=>$v['checkType']??'',
                    'source_type'=>$v['sourceType']??'',
                    'device_id'=>$v['deviceId']??'',
                    'user_address'=>$v['userAddress']??'',
                    'record_ext'=>json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
                ];
            }catch (\Exception $e){
                echo json_encode($v,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
                throw $e;
            }

            empty($columns) && $columns = array_keys($tmp);
            $rows[] = array_values($tmp);
        }
        DingtalkAttendanceRecord::addUpdateColumnRows($columns,$rows);
    }

    /**
     * 钉钉审批数据同步  ding-kaoqin/process-instance
     */
    public function actionProcessInstance(){
        if(exec('ps -ef|grep "ding-kaoqin/process-instance"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $columns=$rows=[];
        $dayList = array_map(function($v){
            return date("Y-m-d",$v);
        },range(strtotime('2019-07-01'),time(),24*3600));
        foreach ($dayList as $day) {
            echo date('Y-m-d H:i:s') . "\t {$day} 开始审批数据到kael\n";
            $resultList = DingtalkAttendanceResult::findListByWhereWithWhereArr(['work_date' => $day], [['!=', 'proc_inst_id', 0]], 'id,proc_inst_id');
            $proc_inst_id_arr = array_unique(array_column($resultList, 'proc_inst_id'));
            $proc_inst_id='69018df6-990c-4f51-a092-219f9f7196e2';
            foreach ($proc_inst_id_arr as $proc_inst_id) {
                $res = DingTalkApi::getProcessInstance($proc_inst_id);
                if ($res['errcode'] == 0) {var_dump($proc_inst_id);
                    $processInstance = $res['process_instance'];
                    $tmp = [
                        'proc_inst_id' => $proc_inst_id,
                        'title' => $processInstance['title'] ?? '',
                        'start_date' => date('Y-m-d', strtotime($processInstance['create_time'])) ?? 0,
                        'start_time' => $processInstance['create_time'],
                        'finish_time' => $processInstance['finish_time'],
                        'user_id' => $processInstance['originator_userid'],
                        'dingtalk_department_id' => $processInstance['originator_dept_id'],
                        'dingtalk_department_name' => $processInstance['originator_dept_name'],
                        'process_status' => $processInstance['status'],

                        'cc_user_id' => $processInstance['cc_userids'] ?? '',
                        'result' => $processInstance['result'],
                        'business_id' => $processInstance['business_id'],
                        'form_component_values' => json_encode($processInstance['form_component_values'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'operation_records' => json_encode($processInstance['operation_records'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'tasks' => json_encode($processInstance['tasks'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),

                        'attached_process_instance_ids' => json_encode($processInstance['attached_process_instance_ids'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)??'',
                        'biz_action' => $processInstance['biz_action']??'',
                    ];
                    empty($columns) && $columns = array_keys($tmp);
                    $rows[] = array_values($tmp);return 4;
                }
            }
            DingtalkAttendanceProcessInstance::addUpdateColumnRows($columns,$rows);
            return 1;
        }
    }
}
