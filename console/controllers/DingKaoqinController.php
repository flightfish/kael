<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkAttendanceResult;
use common\models\DingtalkAttendanceSchedule;
use common\models\DingtalkUser;
use yii\console\Controller;

class DingKaoqinController extends Controller
{

    /**
     * 钉钉考勤信息同步 ding-kaoqin/syn
     */
    public function actionSyn(){
        if(exec('ps -ef|grep "ding-kaoqin/syn"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        echo date('Y-m-d H:i:s')."\t组装用户ID\n";
        $userIds = array_values(array_filter(array_unique(array_column(DingtalkUser::findList([],'','user_id'),'user_id'))));
        //dayList
        $dayList = array_map(function($v){
            return date("Y-m-d",$v);
        },range(time()-7*24*3600,time(),24*3600));
        foreach ($dayList as $day){
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步排班时间数据到kael\n";
            $this->synSchedule($day);
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步考勤数据到kael\n";
            $this->synKaoqin($day,$userIds);
            echo date('Y-m-d H:i:s')."\t {$day} 同步考勤数据结束\n";
        }


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
                'check_type'=>$v['checkType'],
                'time_result'=>$v['timeResult'],
                'location_result'=>$v['locationResult'],
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
}
