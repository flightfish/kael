<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
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

    }
}
