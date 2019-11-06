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


class MeicanController extends Controller
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

    public function actionSynBill()
    {
        /*
        {
            "resultCode": "OK",
            "resultDescription": "查询成功",
            "data": {
                    "orderList": [{
                        "meal": "美餐⽹ 午餐",
                        "time": "2016-01-15 20:00:00",
                        "type": "ONLINE",
                        "dinerCount": 1,
                        "dishCount": 1,
                        "mealList": [{
                            "orderId": "7de347de44aa",
                            "email": "zhangsan@meican.com",
                            "realName": "张三",
                            "employeeId": "123",
                            "orderContent": [{
                                "name": "巨⽆霸",
                                "restaurant": "肯德基",
                                "priceInCent": 1000,
                                "count": 1
                            }]
                        }]
                    },
                    {
                        "meal": "美餐⽹ 到店",
                        "dinerCount": 1,
                        "type": "OFFLINE",
                        "dishCount": 1,
                        "time": "2016-01-15 20:00:00",
                        "mealList": [{
                        "orderId": "Y2LKD5qgLpDwX5M",
                            "employeeId": "123",
                            "postbox": "",
                            "email": "zhangsan@meican.com",
                            "realName": "张三",
                            "orderContent": [{
                            "count": 1,
                                "name": "到店",
                                "priceInCent": 2200,
                                "restaurant": "肯德基"
                            }]
                        }]
                    }
                ]
            }
        }
        */

        //每天下午6点半
        $day = date('Y-m-d');
        $retJson = MeicanApi::listBill($day);
        $columns = [];
        $rows = [];
        $kaelIdToDepartmentId = array_column(DingtalkUser::findList([], '', 'kael_id,department_id'), 'department_id', 'kael_id');
        $departmentIdToInfo = array_column(DingtalkDepartment::findList([], '', 'id,name,subroot_id', -1), null, 'id');
        $departmentIdToInfo[1] = ['id' => 1, 'name' => '小盒科技', 'subroot_id' => 1];
        foreach ($retJson['data']['orderList'] as $mealInfo) {
            foreach ($mealInfo['mealList'] as $orderInfo) {
                $orderInfo['_meal'] = $mealInfo['meal'];
                $orderInfo['_time'] = $mealInfo['time'];
                $orderInfo['_type'] = $mealInfo['type'];
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
                $goods_name = '';
                foreach ($orderInfo['orderContent'] as $vx) {
                    $goods_name .= ' ' . ($vx['name'] ?? '');
                }
                $tmp = [
                    'order_id' => $orderInfo['orderId'],
                    'meal_time' => $mealInfo['time'],
                    'meal_date' => date('Y-m-d', strtotime($mealInfo['time'])),
                    'kael_id' => intval($orderInfo['email']),
                    'order_ext' => json_encode($orderInfo),
                    'supplier' => 1,//1美餐 2竹蒸笼
                    'dingtalk_department_id' => $departmentId,
                    'dingtalk_department_name' => $departmentName,
                    'dingtalk_subroot_id' => $subrootId,
                    'dingtalk_subroot_name' => $subrootName,
                    'price' => array_sum(array_column($orderInfo['orderContent'], 'priceInCent')) / 100,
                    'goods_name' => $goods_name,
                ];
                empty($columns) && $columns = array_keys($tmp);
                $rows[] = array_values($tmp);
            }
        }
        DingcanOrder::addUpdateColumnRows($columns, $rows);
    }

    /**
     * 同步美餐订餐数据
     */
    public function actionDingCanOrder(){
        if(exec('ps -ef|grep "meican/ding-can-order"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $oldDingcanOrder = DingcanOrder::findOneByWhere(['supplier' => 1], '*', 'meal_date desc');
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
            $dayList = [date('Y-m-d')];
        } else {
            echo date('Y-m-d H:i:s')."\t今天美餐数据已经更新完毕\n";
        }
        if(!empty($dayList)){
            foreach ($dayList as $day){
                echo date('Y-m-d H:i:s')."\t {$day} 开始同步美餐订餐数据到kael\n";
                $retJson = MeicanApi::listBill($day);
                $columns = [];
                $rows = [];
                $kaelIdToDepartmentId = array_column(DingtalkUser::findList([], '', 'kael_id,department_id'), 'department_id', 'kael_id');
                $departmentIdToInfo = array_column(DingtalkDepartment::findList([], '', 'id,name,subroot_id', -1), null, 'id');
                $departmentIdToInfo[1] = ['id' => 1, 'name' => '小盒科技', 'subroot_id' => 1];
                foreach ($retJson['data']['orderList'] as $mealInfo) {
                    foreach ($mealInfo['mealList'] as $orderInfo) {
                        $orderInfo['_meal'] = $mealInfo['meal'];
                        $orderInfo['_time'] = $mealInfo['time'];
                        $orderInfo['_type'] = $mealInfo['type'];
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
                        $goods_name = '';
                        foreach ($orderInfo['orderContent'] as $vx) {
                            $goods_name .= ' ' . ($vx['name'] ?? '');
                        }
                        $tmp = [
                            'order_id' => $orderInfo['orderId'],
                            'meal_time' => $mealInfo['time'],
                            'meal_date' => date('Y-m-d', strtotime($mealInfo['time'])),
                            'kael_id' => intval($orderInfo['email']),
                            'order_ext' => json_encode($orderInfo),
                            'supplier' => 1,//1美餐 2竹蒸笼
                            'dingtalk_department_id' => $departmentId,
                            'dingtalk_department_name' => $departmentName,
                            'dingtalk_subroot_id' => $subrootId,
                            'dingtalk_subroot_name' => $subrootName,
                            'price' => array_sum(array_column($orderInfo['orderContent'], 'priceInCent')) / 100,
                            'goods_name' => $goods_name,
                        ];
                        empty($columns) && $columns = array_keys($tmp);
                        $rows[] = array_values($tmp);
                    }
                }
                DingcanOrder::addUpdateColumnRows($columns, $rows);
            }
        }

        //竹蒸笼数据
        ZzlController::SynDingCanOrderZzl();
        $this->SynCanExceptionInit();
    }
    /**
     * 同步异常数据
     */
    public function SynCanExceptionInit()
    {
//        if(exec('ps -ef|grep "meican/can-exception-init"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
//            echo "is_running";
//            exit();
//        }
        echo date('Y-m-d H:i:s') . "\t  开始导入异常订餐数据\n";
        $dingcanOrderExceptionOne = DingcanOrderException::findOneByWhere([], '', 'id desc');
        if (!empty($dingcanOrderExceptionOne)) {
            $startDate = date('Y-m-d', time() - 7 * 24 * 3600);
        } else {
            $startDate = '2019-10-01';
        }
        $dayList = array_map(function ($v) {
            return date("Y-m-d", $v);
        }, range(strtotime($startDate), time(), 24 * 3600));
        $workDayConfig = array_column(WorkDayConfig::findDayConfig($dayList), null, 'day');


        $columns = $rows = [];
        $userList = DingTalkUser::findList([], 'kael_id', 'kael_id,name,user_id');
        foreach ($dayList as $day) {
            echo date('Y-m-d H:i:s') . "\t {$day} 开始同步异常订餐数据\n";
            $dingcanList = DingcanOrder::findListByWhereWithWhereArr(['meal_date' => $day], [], '*');

            $dayConf = $workDayConfig[$day] ?? [];

            if (empty($dayConf['is_allow_dingcan'])) {
                foreach ($dingcanList as $can){
                    $rows[]=$can;
                }
            } else {
                if (!empty($dingcanList)) {
                    $dingcanListIndex = [];
                    foreach ($dingcanList as $v){
                        $dingcanListIndex[$v['kael_id']][] = $v;
                    }
                    $scheduleList = DingtalkAttendanceSchedule::findListByWhereWithWhereArr(
                        ['schedule_date' => $day],
                        [['!=', 'class_id', 0]],
                        'schedule_date,check_type,plan_check_time,user_id');

                    $scheduleListIndex = [];
                    foreach ($scheduleList as $v) {
                        $scheduleListIndex[$v['user_id']][$v['schedule_date'] . ':' . $v['check_type']] = $v;
                    }


                    $resultList = DingtalkAttendanceResult::findListByWhereWithWhereArr(['work_date' => $day
                    ], [], 'work_date,check_type,user_check_time,user_id');
                    $resultListIndex = [];
                    foreach ($resultList as $v) {
                        $resultListIndex[$v['user_id']][$v['work_date'] . ':' . $v['check_type']] = $v;
                    }
                    foreach ($dingcanListIndex as $kaelId=>$canList){
                        if (!isset($userList[$kaelId])) {
                            $offDutySchedule = [];
                            $onDutyResult = [];
                            $offDutyResult = [];
                        } else {
                            $offDutySchedule = $scheduleListIndex[$userList[$kaelId]['user_id']][$day . ':OffDuty'] ?? [];
                            $onDutyResult = $resultListIndex[$userList[$kaelId]['user_id']][$day . ':OnDuty'] ?? [];
                            $offDutyResult = $resultListIndex[$userList[$kaelId]['user_id']][$day . ':OffDuty'] ?? [];
                        }

                        //工作日9点
                        if (
                            isset($offDutySchedule['plan_check_time']) && (
                                !isset($offDutyResult['user_check_time']) ||
                                $offDutyResult['user_check_time'] < $day . ' 21:00:00')

                        ) {
                            foreach ($canList as $can){
                                $rows[]=$can;
                            }

                        }

                        //非工作日
                        elseif (!isset($offDutyResult['user_check_time']) &&
                            !isset($onDutyResult['user_check_time'])&&
                            !isset($offDutySchedule['plan_check_time'])
                        ) {
                            //未打卡
                            foreach ($canList as $can){
                                $rows[]=$can;
                            }
                        }
                    }
                }

            }

        }

        if (empty($columns)) {
            $columns = array_keys($rows[0]);
        }
        DingcanOrderException::addUpdateColumnRows($columns, $rows);

        $this->canExceptionUpdate();
    }
    /**
     * 更新异常数据
     */
    public function canExceptionUpdate(){
        echo date('Y-m-d H:i:s') . "\t  异常订餐数据校验,40天内\n";
        $startDate = date('Y-m-d', strtotime("-40 days"));
        $dayList = array_map(function ($v) {
            return date("Y-m-d", $v);
        }, range(strtotime($startDate), time(), 24 * 3600));
        $workDayConfig = array_column(WorkDayConfig::findDayConfig($dayList), null, 'day');

        $userList = DingTalkUser::findList([], 'kael_id', 'kael_id,name,user_id');
        foreach ($dayList as $day) {
            echo date('Y-m-d H:i:s') . "\t {$day} 开始异常订餐--重新校验\n";
            $dingcanList = DingcanOrderException::findListByWhereWithWhereArr(['meal_date' => $day], [], '*');

            $dayConf = $workDayConfig[$day] ?? [];

            if (!empty($dayConf['is_allow_dingcan'])) {
                if (!empty($dingcanList)) {
                    $dingcanListIndex = [];
                    foreach ($dingcanList as $v){
                        $dingcanListIndex[$v['kael_id']][] = $v;
                    }
                    $scheduleList = DingtalkAttendanceSchedule::findListByWhereWithWhereArr(
                        ['schedule_date' => $day],
                        [['!=', 'class_id', 0]],
                        'schedule_date,check_type,plan_check_time,user_id');

                    $scheduleListIndex = [];
                    foreach ($scheduleList as $v) {
                        $scheduleListIndex[$v['user_id']][$v['schedule_date'] . ':' . $v['check_type']] = $v;
                    }


                    $resultList = DingtalkAttendanceResult::findListByWhereWithWhereArr(['work_date' => $day
                    ], [], 'work_date,check_type,user_check_time,user_id');
                    $resultListIndex = [];
                    foreach ($resultList as $v) {
                        $resultListIndex[$v['user_id']][$v['work_date'] . ':' . $v['check_type']] = $v;
                    }
                    foreach ($dingcanListIndex as $kaelId=>$canList){
                        if (!isset($userList[$kaelId])) {
                            $offDutySchedule = [];
                            $onDutyResult = [];
                            $offDutyResult = [];
                        } else {
                            $offDutySchedule = $scheduleListIndex[$userList[$kaelId]['user_id']][$day . ':OffDuty'] ?? [];
                            $onDutyResult = $resultListIndex[$userList[$kaelId]['user_id']][$day . ':OnDuty'] ?? [];
                            $offDutyResult = $resultListIndex[$userList[$kaelId]['user_id']][$day . ':OffDuty'] ?? [];
                        }

                        //工作日9点
                        if (
                            isset($offDutySchedule['plan_check_time']) &&
                            isset($offDutyResult['user_check_time']) &&
                            $offDutyResult['user_check_time'] >= $day . ' 21:00:00'

                        ) {
                            DingcanOrderException::updateAll(['status' => 1], ['id' => $canList[0]['id']]);
                        }
                        //非工作日
                        elseif (
                            (isset($offDutyResult['user_check_time']) || !isset($onDutyResult['user_check_time']))
                            && !isset($offDutySchedule['plan_check_time'])
                        ) {
                            DingcanOrderException::updateAll(['status' => 1], ['id' => $canList[0]['id']]);
                        }
                    }
                }
            }
        }
    }

}
