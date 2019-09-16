<?php
namespace console\controllers;

use common\libs\DingTalkApi;
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
        $this->synKaoqin();
        echo date('Y-m-d H:i:s')."\t同步考勤数据结束\n";
    }

    public function synKaoqin(){
        $scheduleList = DingTalkApi::getAttendanceListSchedule('2019-09-10');
        echo json_encode($scheduleList,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        return false;
        $list = DingTalkApi::getAttendanceList(
            '2019-08-28 00:00:00',
            '2019-08-31 23:59:59',
            [
                '00036',
//                '15243079933019240',
                '15667442833927047'
            ]);
        foreach ($list as $v){
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
            isset($v['workDate']) && $v['workDate'] = date("Y-m-d H:i:s",intval($v['workDate']/1000));
            isset($v['userCheckTime']) && $v['userCheckTime'] = date("Y-m-d H:i:s",intval($v['userCheckTime']/1000));
            echo json_encode($v,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
        }
    }
}
