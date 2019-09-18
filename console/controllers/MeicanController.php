<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
use common\models\DingcanOrder;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use usercenter\modules\meican\models\MeicanApi;
use Yii;
use yii\console\Controller;


class MeicanController extends Controller
{
    /**
     * 初始化用户信息到Meican
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "meican/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        if(empty(Yii::$app->params['meican_corp_prefix'])){
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
            foreach ($allMembers as $v){
                if(empty($v['removed'])){
                    $allMemberUserIds[] = intval($v['email']);
                    $userIdToDepartment[intval($v['email'])] = in_array($v['department'],$allowDepartment) ? $v['department'] : $allowDepartment[1];
                    $userIdToRealName[intval($v['email'])] = $v['realName'];
                }
            }
            //全部有效的
            $allValidUserInfoList = CommonUser::find()
                ->select('a.id,a.username,b.department_subroot,c.`name` as department_name')
                ->from('user a')
                ->leftJoin('dingtalk_user b','a.work_number = b.job_number')
                ->leftJoin('dingtalk_department c','b.department_subroot = c.id')
                ->where(['a.user_type'=>0,'a.status'=>0,'b.status'=>0])
                ->andWhere(['!=','a.work_number',''])
                ->orderBy('a.work_number asc')
                ->createCommand()
                ->queryAll();
            $allValidUserIds = array_column($allValidUserInfoList,'id');
            $allValidUserIds = array_map('intval',$allValidUserIds);
            //删除旧的
            $delUserIds = array_diff($allMemberUserIds,$allValidUserIds);
            foreach ($allValidUserInfoList as $v){
//                echo $v['id']."\n";
                if($v['department_subroot'] == 1){
                    $vDept = $allowDepartment[0];
                }elseif(in_array($v['department_name'],$allowDepartment)){
                    $vDept = $v['department_name'];
                }else{
                    $vDept = $allowDepartment[1];
                }
              //  echo "{$v['id']}-{$v['username']}-{$vDept}-{$v['department_name']}\n";
                if(
                    empty($userIdToDepartment[$v['id']]) || $userIdToDepartment[$v['id']] != $vDept
                    || empty($userIdToRealName[$v['id']]) || $userIdToRealName[$v['id']] != $v['username']
                ){
                    try{
                        MeicanApi::addMember($v['id'],$v['username'],$vDept);
                    }catch (\Exception $e){
                        echo date("Y-m-d H:i:s")."-addmember-{$v["id"]}-{$v['username']}-".strval($e->getMessage());
                        continue;
                    }
                }
            }
            foreach ($delUserIds as $v){
                try{
                    MeicanApi::delMember($v);
                }catch (\Exception $e){
                    echo date("Y-m-d H:i:s")."-delmember-{$v["id"]}-{$v['username']}-".strval($e->getMessage());
                    continue;
                }
            }
        }catch (\Exception $e){
            throw $e;
        }

    }

    public function actionSynBill(){
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

        //每天6点半
        $day = date('Y-m-d');
        $retJson = MeicanApi::listBill($day);
        $columns = [];
        $rows = [];
        $kaelIdToDepartmentId = array_column(DingtalkUser::findList([],'','kael_id,department_id'),'department_id','kael_id');
        $departmentIdToInfo = array_column(DingtalkDepartment::findList([],'','id,name,subroot_id',-1),null,'id');
        $departmentIdToInfo[1] = ['id'=>1,'name'=>'小盒科技','subroot_id'=>1];
        foreach ($retJson['data']['orderList'] as $mealInfo){
            foreach ($mealInfo['mealList'] as $orderInfo){
                $orderInfo['_meal'] = $mealInfo['meal'];
                $orderInfo['_time'] = $mealInfo['time'];
                $orderInfo['_type'] = $mealInfo['type'];
                $kaelId = intval($orderInfo['email']);
                $departmentId = $kaelIdToDepartmentId[$kaelId] ?? 0;
                $departmentName = '';
                $subrootId = 0;
                $subrootName = '';
                $departmentInfo = $departmentIdToInfo[$departmentId] ?? [];
                if(!empty($departmentInfo)){
                    $departmentName = $departmentInfo['name'];
                    $subrootId = $departmentInfo['subroot_id'];
                    $subrootName = ($departmentIdToInfo[$subrootId] ?? [])['name'] ?? '';
                }
                $tmp = [
                    'order_id'=>$orderInfo['orderId'],
                    'meal_time'=>$mealInfo['time'],
                    'meal_date'=>date('Y-m-d',strtotime($mealInfo['time'])),
                    'kael_id'=>intval($orderInfo['email']),
                    'order_ext'=>json_encode($orderInfo),
                    'supplier'=>1,//1美餐 2竹蒸笼
                    'dingtalk_department_id'=>$departmentId,
                    'dingtalk_department_name'=>$departmentName,
                    'dingtalk_subroot_id'=>$subrootId,
                    'dingtalk_subroot_name'=>$subrootName,
                    'price'=>array_sum(array_column($orderInfo['orderContent'],'priceInCent'))/100
                ];
                empty($columns) && $columns = array_keys($tmp);
                $rows[] = array_values($tmp);
            }
        }
        DingcanOrder::addUpdateColumnRows($columns,$rows);
    }
}
