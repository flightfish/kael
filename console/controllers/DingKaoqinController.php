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
        $ret = DingTalkApi::getAttendanceListRecord(
            '2019-09-11 00:00:00',
            '2019-09-16 23:59:59',
            [
//                '00036',
//                '15243079933019240',
                '15667442833927047'
            ]);
        foreach ($ret['recordresult'] as $v){
            /**
             * {
            "gmtModified": 1568599964000,
            "baseCheckTime": 1568599200000,
            "groupId": 160790019,
            "timeResult": "Late",
            "deviceId": "535321c9164b8fe9a75dc4fcc471b25",
            "baseLongitude": 116.480198,
            "userAccuracy": 550,
            "classId": 80240005,
            "workDate": 1568563200000,
            "planId": 72491443063,
            "id": 22752893818,
            "baseAccuracy": 0,
            "checkType": "OnDuty",
            "planCheckTime": 1568599200000,
            "corpId": "ding56f88c485c1f3d8e35c2f4657eb6378f",
            "locationResult": "Normal",
            "userLongitude": 116.474133,
            "isLegal": "N",
            "baseAddress": "阜安西路",
            "gmtCreate": 1568599964000,
            "userId": "15243079933019240",
            "userAddress": "望京SOHO中心T2",
            "userLatitude": 39.997954,
            "baseLatitude": 39.997037,
            "sourceType": "AUTO_CHECK",
            "userCheckTime": 1568599964000,
            "locationMethod": "MAP"
            }
             */
            isset($v['gmtModified']) && $v['gmtModified'] = date("Y-m-d H:i:s",intval($v['gmtModified']/1000));
            isset($v['baseCheckTime']) && $v['baseCheckTime'] = date("Y-m-d H:i:s",intval($v['baseCheckTime']/1000));
            isset($v['workDate']) && $v['workDate'] = date("Y-m-d H:i:s",intval($v['workDate']/1000));
            isset($v['gmtCreate']) && $v['gmtCreate'] = date("Y-m-d H:i:s",intval($v['gmtCreate']/1000));
            isset($v['userCheckTime']) && $v['userCheckTime'] = date("Y-m-d H:i:s",intval($v['userCheckTime']/1000));
            echo json_encode($v,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
        }
    }
}
