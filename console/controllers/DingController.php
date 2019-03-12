<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use Yii;
use yii\console\Controller;


class DingController extends Controller
{
    /**
     * 初始化钉钉信息
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "ding/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $this->updateDingDepartment();
        $this->updateDingUser();
    }

    /**
     * 查询回调
     */
    public function actionQueryCallback(){
        $ret = DingTalkApi::callBackQuery();
        echo json_encode($ret,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    private function updateDingDepartment(){
        $allDepartmentList = DingTalkApi::getDepartmentAllList();
        $allIds = array_column($allDepartmentList,'id');
        $oldDepartmentIds = DingtalkDepartment::find()->select('id')->where(['status'=>0])->asArray(true)->column();
        $oldDepartmentIds = array_map('intval',$oldDepartmentIds);
        $delIds = array_diff($oldDepartmentIds,$allIds);
        $insertIds  = array_diff($allIds,$oldDepartmentIds);
        $columns = ['id','name','parentid'];
        $rows = [];
        foreach ($allDepartmentList as $v){
            if(in_array($v['id'],$oldDepartmentIds)){
                DingtalkDepartment::updateAll(['name'=>$v['name'],'parentid'=>$v['parentid']],['id'=>$v['id']]);
            }elseif(in_array($v['id'],$insertIds)){
                $rows[] = [$v['id'],$v['name'],$v['parentid']];
            }
        }
        !empty($rows) && DingtalkDepartment::batchInsertAll(DingtalkDepartment::tableName(),$columns,$rows,DingtalkDepartment::getDb(),'INSERT IGNORE');
        !empty($delIds) && DingtalkDepartment::updateAll(['status'=>0]);
        //更新level
        $sql = "update dingtalk_department set `level` = 1,`subroot_id` = id where status = 0 and parentid = 1";
        DingtalkDepartment::getDb()->createCommand($sql)->execute();
        for($level =1 ; $level <= 10; $level++){
            $sql = "update dingtalk_department a left join dingtalk_department b on a.parentid = b.id set a.`level` = b.level + 1,a.`subroot_id` = b.subroot_id where a.status = 0 and b.status = 0 and b.`level`={$level}";
            DingtalkDepartment::getDb()->createCommand($sql)->execute();
        }
    }

    private function updateDingUser(){
        $allUserIds = DingtalkUser::find()->select('user_id')->where(['status'=>0])->column();
        $currentUserIds = [];
        $departmentToSubRoot = DingtalkDepartment::find()->select('id,subroot_id')
            ->where(['status'=>0])->asArray(true)->all();
        $departmentToSubRoot = array_column($departmentToSubRoot,'subroot_id','id');
        for($level = 1; $level <= 10 ;$level ++){
            $departmentList = DingtalkDepartment::find()->where(['status'=>0,'level'=>$level])
                ->asArray(true)->all();
            foreach ($departmentList as $v) {
                $userIdList = DingTalkApi::getDepartmentUserIds($v['id']);
                foreach ($userIdList as $userId){
                    if(in_array($userId,$currentUserIds)){
                        continue;
                    }
                    $currentUserIds[] = $userId;
                    $userInfo = DingTalkApi::getUserInfo($userId);
                    echo json_encode($userInfo)."\n";
                    if(in_array($userId,$allUserIds)){
                        //更新
                        DingtalkUser::updateAll(
                            [
                                'name'=>$userInfo['name'],
                                'email'=>$userInfo['email'] ?? "",
                                'mobile'=>$userInfo['mobile'],
                                'avatar'=>$userInfo['avatar'],
                                'job_number'=>$userInfo['jobnumber'],
                                'union_id'=>$userInfo['unionid'],
                                'open_id'=>$userInfo['openId'],
                                'departments'=>join(',',$userInfo['department']),
                                'department_id'=>$userInfo['department'][0],
                                'department_subroot'=>$departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                            ],
                            ['user_id'=>$userInfo['userid']]);
                    }else{
                        //新增
                        DingtalkUser::add([
                            'user_id'=>$userInfo['userid'],
                            'name'=>$userInfo['name'],
                            'email'=>$userInfo['email'] ?? "",
                            'mobile'=>$userInfo['mobile'],
                            'avatar'=>$userInfo['avatar'],
                            'job_number'=>$userInfo['jobnumber'],
                            'union_id'=>$userInfo['unionid'],
                            'open_id'=>$userInfo['openId'],
                            'departments'=>join(',',$userInfo['department']),
                            'department_id'=>$userInfo['department'][0],
                            'department_subroot'=>$departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                        ]);
                    }
                }
            }
        }
    }
}
