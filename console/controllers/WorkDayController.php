<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DBCommon;
use common\models\DingtalkAttendanceRecord;
use common\models\DingtalkAttendanceResult;
use common\models\DingtalkAttendanceSchedule;
use common\models\DingtalkUser;
use common\models\WorkDayConfig;
use yii\console\Controller;

class WorkDayController extends Controller
{

    /**
     * 钉钉考勤信息初始化 work-day/init
     */
    public function actionInit(){
        if(exec('ps -ef|grep "work-day/init"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $columns = ['day','is_work_day','is_allow_dingcan'];
        $rows = [];
        $day = '2019-01-01';
        while(1){
            if($day >= '2025-01-01'){
                break;
            }
            $w = date("w",strtotime($day));
            $isWorkDay = in_array($w,[1,2,3,4,5]) ? 1 : 0;
            $isAllowDingcan = in_array($w,[1,2,3,4,5,6]) ? 1 : 0;
            $rows[] = [
                $day,
                $isWorkDay,
                $isAllowDingcan,
            ];
            $day = date("Y-m-d",strtotime($day.' +1day'));
        }

        DBCommon::batchInsertAll(WorkDayConfig::tableName(),$columns,$rows,WorkDayConfig::getDb());
    }
}
