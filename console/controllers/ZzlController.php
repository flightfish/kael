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
     * 初始化用户信息到Meican
     */
    public function actionUpdate()
    {
        if (exec('ps -ef|grep "meican/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1) {
            echo "is_running";
            exit();
        }
        if (empty(Yii::$app->params['meican_corp_prefix'])) {
            echo "未设置美餐信息\n";
            exit();
        }
        $allowDepartment = Yii::$app->params['meican_department'];
        try {
            $allMembers = MeicanApi::listMember();
//            echo date('Y-m-d H:i:s ').json_encode($allMembers,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
            $allMemberUserIds = [];
            $userIdToDepartment = [];
            $userIdToRealName = [];
            foreach ($allMembers as $v) {
                if (empty($v['removed'])) {
                    $allMemberUserIds[] = intval($v['email']);
                    $userIdToDepartment[intval($v['email'])] = in_array($v['department'], $allowDepartment) ? $v['department'] : $allowDepartment[1];
                    $userIdToRealName[intval($v['email'])] = $v['realName'];
                }
            }
            //全部有效的
            $allValidUserInfoList = CommonUser::find()
                ->select('a.id,a.username,b.department_subroot,c.`name` as department_name')
                ->from('user a')
                ->leftJoin('dingtalk_user b', 'a.work_number = b.job_number')
                ->leftJoin('dingtalk_department c', 'b.department_subroot = c.id')
                ->where(['a.user_type' => 0, 'a.status' => 0, 'b.status' => 0, 'b.corp_type' => 1])
                ->andWhere(['!=', 'a.work_number', ''])
                ->orderBy('a.work_number asc')
                ->createCommand()
                ->queryAll();
            $allValidUserIds = array_column($allValidUserInfoList, 'id');
            $allValidUserIds = array_map('intval', $allValidUserIds);
            //删除旧的
            $delUserIds = array_diff($allMemberUserIds, $allValidUserIds);
            foreach ($allValidUserInfoList as $v) {
//                echo $v['id']."\n";
                if ($v['department_subroot'] == 1) {
                    $vDept = $allowDepartment[0];
                } elseif (in_array($v['department_name'], $allowDepartment)) {
                    $vDept = $v['department_name'];
                } else {
                    $vDept = $allowDepartment[1];
                }
                //  echo "{$v['id']}-{$v['username']}-{$vDept}-{$v['department_name']}\n";
                if (
                    empty($userIdToDepartment[$v['id']]) || $userIdToDepartment[$v['id']] != $vDept
                    || empty($userIdToRealName[$v['id']]) || $userIdToRealName[$v['id']] != $v['username']
                ) {
                    try {
                        MeicanApi::addMember($v['id'], $v['username'], $vDept);
                    } catch (\Exception $e) {
                        echo date("Y-m-d H:i:s") . "-addmember-{$v["id"]}-{$v['username']}-" . strval($e->getMessage());
                        continue;
                    }
                }
            }
            foreach ($delUserIds as $v) {
                try {
                    MeicanApi::delMember($v);
                } catch (\Exception $e) {
                    echo date("Y-m-d H:i:s") . "-delmember-{$v["id"]}-{$v['username']}-" . strval($e->getMessage());
                    continue;
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

    }



    /**
     * 同步竹蒸笼订餐数据
     */
    public function actionDingCanOrder(){
        if(exec('ps -ef|grep "zzl/ding-can-order"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $oldDingcanOrder = DingcanOrder::findOneByWhere(['supplier' => 2], '*', 'meal_date desc');
        if (empty($oldDingcanOrder)) {
            $start = "2019-11-05";
        } else {
            $start = $oldDingcanOrder['meal_date'];
        }     $start = "2019-11-05";
        $dayList = array_map(function ($v) {
            return date("Y-m-d", $v);
        }, range(strtotime($start), time(), 24 * 3600));

        foreach ($dayList as $day){
            echo date('Y-m-d H:i:s')."\t {$day} 开始同步竹蒸笼订餐数据到kael\n";
            self::SynZzlDay($day);
        }
    }

    public static function SynZzlDay($day){
        $retJson = ZzlApi::orderList($day);
        $columns = [];
        $rows = [];
        $kaelIdToDepartmentId = array_column(DingtalkUser::findList([], '', 'kael_id,department_id'), 'department_id', 'kael_id');
        $departmentIdToInfo = array_column(DingtalkDepartment::findList([], '', 'id,name,subroot_id', -1), null, 'id');
        $departmentIdToInfo[1] = ['id' => 1, 'name' => '小盒科技', 'subroot_id' => 1];
        foreach ($retJson['data'] as $orderInfo) {
            $retJson = ZzlApi::userList();
            var_dump($retJson);die;

            $kaelId = intval($orderInfo['email']);
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
                'order_id' => $orderInfo['orderId'],
                'meal_time' => date('Y-m-d H:i:d', $orderInfo['add_time']),
                'meal_date' => date('Y-m-d', strtotime($orderInfo['time'])),
                'kael_id' => intval($orderInfo['email']),
                'order_ext' => json_encode($orderInfo),
                'supplier' => 2,//1美餐 2竹蒸笼
                'dingtalk_department_id' => $departmentId,
                'dingtalk_department_name' => $departmentName,
                'dingtalk_subroot_id' => $subrootId,
                'dingtalk_subroot_name' => $subrootName,
                'price' => $orderInfo['goods_price']
            ];
            empty($columns) && $columns = array_keys($tmp);
            $rows[] = array_values($tmp);
        }
        DingcanOrder::addUpdateColumnRows($columns, $rows);
    }

}
