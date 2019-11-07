<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
use common\models\DingcanOrder;
use common\models\DingcanOrderException;
use common\models\DingtalkAttendanceRecord;
use common\models\DingtalkAttendanceResult;
use common\models\DingtalkAttendanceSchedule;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use common\models\WorkDayConfig;
use usercenter\modules\meican\models\MeicanApi;
use usercenter\modules\meican\models\ZzlApi;
use Yii;
use yii\console\Controller;


class ZzlController extends Controller
{
    /**
     * 同步竹蒸笼订餐数据
     */
    public static function SynDingCanOrderZzl(){
//        if(exec('ps -ef|grep "zzl/ding-can-order"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
//            echo "is_running";
//            exit();
//        }
        $oldDingcanOrder = DingcanOrder::findOneByWhere(['supplier' => 2], '*', 'meal_date desc');
        if (empty($oldDingcanOrder)) {
            $start = "2019-10-01";
        } else {
            $start = date('Y-m-d',strtotime($oldDingcanOrder['meal_date'])+24 * 3600);
        }
        if ($start < date('Y-m-d')) {
            $dayList = array_map(function ($v) {
                return date("Y-m-d", $v);
            }, range(strtotime($start), time(), 24 * 3600));
        } elseif ($start == date('Y-m-d')) {
            $dayList = [date('Y-m-d', time() - 24 * 3600), date('Y-m-d')];
        } else {
            $dayList = [date('Y-m-d')];
            echo date('Y-m-d H:i:s')."\t今天竹蒸笼数据再次更新\n";

        }
        if(!empty($dayList)){
            foreach ($dayList as $day){
                echo date('Y-m-d H:i:s')."\t {$day} 开始同步竹蒸笼订餐数据到kael\n";
                $retJson = ZzlApi::orderList($day);
                $columns = [];
                $rows = [];
                $kaelIdToDepartmentId = array_column(DingtalkUser::findList([], '', 'kael_id,department_id',-1), 'department_id', 'kael_id');
                $departmentIdToInfo = array_column(DingtalkDepartment::findList([], '', 'id,name,subroot_id', -1), null, 'id');
                $departmentIdToInfo[1] = ['id' => 1, 'name' => '小盒科技', 'subroot_id' => 1];
                if (!empty($retJson['data'])) {
                    foreach ($retJson['data'] as $orderInfo) {
                        $kaelId = intval(substr($orderInfo['userid'], 1));
                        $departmentId = $kaelIdToDepartmentId[$kaelId] ?? 0;
                        $departmentName = '';
                        $subrootId = 0;
                        $subrootName = '';
                        $departmentInfo = $departmentIdToInfo[$departmentId] ?? [];
                        if (!empty($departmentInfo)) {
                            $departmentName = $departmentInfo['name'];
                            $subrootId = $departmentInfo['subroot_id'];
                            $subrootName = ($departmentIdToInfo[$subrootId] ?? [])['name'] ?? '';
                        }
                        $tmp = [
                            'order_id' => 'Zzl-' . $orderInfo['add_time'] . intval(substr($orderInfo['userid'], 1)),
                            'meal_time' => date('Y-m-d H:i:s', $orderInfo['add_time']),
                            'meal_date' => date('Y-m-d', $orderInfo['add_time']),
                            'kael_id' => intval(substr($orderInfo['userid'], 1)),
                            'order_ext' => json_encode($orderInfo),
                            'supplier' => 2,//1美餐 2竹蒸笼
                            'dingtalk_department_id' => $departmentId,
                            'dingtalk_department_name' => $departmentName,
                            'dingtalk_subroot_id' => $subrootId,
                            'dingtalk_subroot_name' => $subrootName,
                            'price' => $orderInfo['goods_price'],
                            'goods_name' => $orderInfo['goods_name'] ?? '',
                        ];
                        empty($columns) && $columns = array_keys($tmp);
                        $rows[] = array_values($tmp);
                    }
                }
                DingcanOrder::addUpdateColumnRows($columns, $rows);
            }
        }
    }

}
