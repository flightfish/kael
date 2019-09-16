<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkAttendanceResult;
use common\models\DingtalkAttendanceSchedule;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use common\models\ehr\AuthUser;
use common\models\ehr\AuthUserRoleDataPermRecord;
use common\models\ehr\AuthUserRoleRecord;
use common\models\ehr\BusinessDepartment;
use common\models\DepartmentRelateToKael;
use common\models\ehr\BusinessLineRelateSecondLeader;
use common\models\ehr\BusinessLineRelateStaff;
use common\models\ehr\BusinessLineVersionModel;
use common\models\ehr\ConcernAnniversaryRecord;
use common\models\ehr\ConcernBirthdayRecord;
use common\models\ehr\DepartmentUser;
use common\models\ehr\PsAnswer;
use common\models\ehr\PsEvaluateRelate;
use common\models\ehr\PsMessageDetail;
use common\models\ehr\PushCenterAcceptUserRecord;
use common\models\ehr\PushCenterLog;
use common\models\ehr\StaffFieldEditRecord;
use common\models\UserCenter;
use common\models\UserInfo;
use Yii;
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
        echo date('Y-m-d H:i:s')."\t开始同步考勤数据到kael\n";
        $this->synKaoqin(date("Y-m-d"));
        echo date('Y-m-d H:i:s')."\t同步考勤数据结束\n";
    }

    public function synKaoqin($day){
        //排班时间
        $scheduleList = DingTalkApi::getAttendanceListSchedule(date('Y-m-d',strtotime($day)));
        foreach ($scheduleList as $v){
            echo json_encode($v,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
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
            DingtalkAttendanceSchedule::add([
                'plan_id'=>$v['plan_id'],
                'schedule_date'=>date("Y-m-d",strtotime($v['plan_check_time'])),
                'check_type'=>$v['check_type'],
                'approve_id'=>$v['approve_id'] ?? 0,
                'user_id'=>$v['userid'],
                'class_id'=>$v['class_id'],
                'class_setting_id'=>$v['class_setting_id'],
                'plan_check_time'=>$v['plan_check_time'],
                'group_id'=>$v['group_id'],
            ]);
            /**
            "plan_id":1,
            "check_type":"OnDuty",
            "approve_id":1,
            "userid":"0001",
            "class_id":1,
            "class_setting_id":1,
            "plan_check_time":"2017-04-11 11:11:11",
            "group_id":1
             */
        }
        //考勤时间
        $attendanceList = DingTalkApi::getAttendanceList(
            date('Y-m-d 00:00:00',strtotime($day)),
            date('Y-m-d 23:59:59',strtotime($day)),
            [
                '00036',
                '15243079933019240',
                '15667442833927047'
            ]);
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

            DingtalkAttendanceResult::add([
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
            ]);
        }
    }
}
