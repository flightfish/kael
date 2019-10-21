<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\libs\DingTalkApiJZ;
use common\models\CommonUser;
use common\models\DingtalkDepartment;
use common\models\DingtalkDepartmentUser;
use common\models\DingtalkUser;
use common\models\DepartmentRelateToKael;
use usercenter\components\exception\Exception;
use yii\console\Controller;

class DingNewController extends Controller
{


    public function actionTest(){
        $userInfoList = DingTalkApi::getDepartmentUserInfoList(1);
        echo json_encode($userInfoList);
    }


    /**
     * 初始化钉钉信息
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "ding-new/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        try{

            echo date('Y-m-d H:i:s')."\t开始同步钉钉部门到kael\n";
            $this->updateDingDepartment(1);
            echo date('Y-m-d H:i:s')."\t部门同步结束\n";

            echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉部门到kael\n";
            $this->updateDingDepartment(2);
            echo date('Y-m-d H:i:s')."\t部门同步兼职团队结束\n";

            echo date('Y-m-d H:i:s')."\t部门同步path_name开始\n";
            $this->updateDingDepartmentPathName();
            echo date('Y-m-d H:i:s')."\t部门同步path_name结束\n";

            sleep(1);

            echo date('Y-m-d H:i:s')."\t开始同步钉钉人员到kael\n";
            $this->updateDingUser(1);
            echo date('Y-m-d H:i:s')."\t员工同步结束\n";

            echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉人员到kael\n";
            $this->updateDingUser(2);
            echo date('Y-m-d H:i:s')."\t员工同步兼职团队结束\n";

        }catch (\Exception $e){
            throw $e;
        }
    }


    private function updateDingDepartment($corpType){
        if($corpType == 1){
            $allDepartmentList = DingTalkApi::getDepartmentAllList();
        }elseif($corpType == 2){
            $allDepartmentList = DingTalkApiJZ::getDepartmentAllList();
        }else{
            throw new Exception("不支持的企业");
        }
        $allIds = array_column($allDepartmentList,'id');
        DingtalkDepartment::updateAll(['corp_type'=>$corpType,'status'=>0],['id'=>$allIds]);

        $oldDepartments = array_column(
            DingtalkDepartment::find()
                ->select('*')
                ->where(['status'=>0,'corp_type'=>$corpType])
                ->asArray(true)->all(),
            null,
            'id'
        );
        $oldDepartmentIds = array_keys($oldDepartments);
        $oldDepartmentIds = array_map('intval',$oldDepartmentIds);
        $delIds = array_diff($oldDepartmentIds,$allIds);
        $insertIds  = array_diff($allIds,$oldDepartmentIds);
        echo date('Y-m-d H:i:s')."\t新增部门如下:\n";
        echo json_encode($insertIds)."\n";
        echo date('Y-m-d H:i:s')."\t需要删除部门如下:\n";
        echo json_encode($delIds)."\n";
        $columns = ['id','name','alias_name','parentid','corp_type'];
        $rows = [];
        foreach ($allDepartmentList as $v){
            if(in_array($v['id'],$oldDepartmentIds)){
                $params = ['name'=>$v['name'],'parentid'=>$v['parentid']];
                if(empty($oldDepartments[$v['id']]['alias_name'])){
                    $params['alias_name'] = $v['name'];
                }
                if($oldDepartments[$v['id']]['main_leader_id'] && ! $user = DingtalkUser::findOneByWhere(['kael_id'=>$oldDepartments[$v['id']]['main_leader_id']])){
                    echo date('Y-m-d H:i:s')."\t部门负责人离职,重置为空,部门编号:".$v['id']."\t原部门负责人编号:".$oldDepartments[$v['id']]['main_leader_id']."\n";
                    $params['main_leader_id'] = 0;
                    $params['main_leader_name'] = '';
                }
                DingtalkDepartment::updateAll($params,['id'=>$v['id']]);
            }elseif(in_array($v['id'],$insertIds)){
                $rows[] = [$v['id'],$v['name'],$v['name'],$v['parentid'],1];
            }
        }
        !empty($rows) && DingtalkDepartment::batchInsertAll(DingtalkDepartment::tableName(),$columns,$rows,DingtalkDepartment::getDb(),'INSERT IGNORE');
        if(!empty($delIds)){
            DingtalkDepartment::updateAll(['status'=>1],['id'=>$delIds]);
            DepartmentRelateToKael::updateAll(['status'=>1],['department_id'=>$delIds]);
        }
        //更新level
        $sql = "update dingtalk_department set `level` = 1,`subroot_id` = id where status = 0 and parentid = 1";
        DingtalkDepartment::getDb()->createCommand($sql)->execute();
        for($level =1 ; $level <= 10; $level++){
            $sql = "update dingtalk_department a left join dingtalk_department b on a.parentid = b.id set a.`level` = b.level + 1,a.`subroot_id` = b.subroot_id where a.status = 0 and b.status = 0 and b.`level`={$level}";
            DingtalkDepartment::getDb()->createCommand($sql)->execute();
        }
    }

    private function updateDingUser($corpType)
    {
        //所有企业员工
        $allDepartmentUserAll = DingtalkDepartmentUser::findList(['corp_type'=>$corpType], '', 'user_id,department_id,relate_id');
        $allDepartmentUserIndex = [];
        foreach ($allDepartmentUserAll as $v){
            $allDepartmentUserIndex[$v['department_id'].'|'.$v['user_id']] = $v['relate_id'];
        }
        //所有kael信息
        $kaelInfoList = CommonUser::find()->select('id,mobile')->where(['status'=>0])->asArray(true)->all();
        $mobileToKaelId = array_column($kaelInfoList,'id','mobile');
        $i = 0;
        for ($level = 0; $level < 10; $level++) {
            echo "level: ".$level."\n";
            if($level == 0){
                $departmentList = [
                    ['id'=>1,'name'=>'小盒科技']
                ];
            }else{
                $departmentList = DingtalkDepartment::find()
                    ->where(['status' => 0, 'level' => $level,'corp_type'=>$corpType])
                    ->orderBy('id')
                    ->asArray(true)
                    ->all();
            }
            foreach ($departmentList as $v) {
                echo "dept: {$v['id']}\n";
                $i++;
                if($corpType == 1){
                    $userInfoList = DingTalkApi::getDepartmentUserInfoList($v['id']);
                }elseif($corpType == 2){
                    $userInfoList = DingTalkApiJZ::getDepartmentUserInfoList($v['id']);
                }else{
                    throw new Exception("不支持的企业");
                }
                echo date('Y-m-d H:i:s') . "\t同步部门人员：{$v['name']}[{$v['id']}]\n";
                foreach ($userInfoList as $userInfo) {
                    echo "第" . $i . "次执行*******\n";

                    $userId = $userInfo['userid'];
                    $relateId = 0;
                    if(isset($allDepartmentUserIndex[$v['id'].'|'.$userId])){
                        $relateId = $allDepartmentUserIndex[$v['id'] . '|' . $userId];
                        unset($allDepartmentUserIndex[$v['id'].'|'.$userId]);
                    }

                    $userParams = [
                        'corp_type' => $corpType,
                        'department_id' => $v['id'],
                        'kael_id'=>$mobileToKaelId[$userInfo['mobile']] ?? 0,
                        'user_id' => $userId,
                        'union_id' => $userInfo['unionid'],
                        'open_id' => $userInfo['openid'] ?? '',
                        'mobile' => $userInfo['mobile'],
                        'state_code' => $userInfo['stateCode']??'',
                        'tel' => $userInfo['tel'] ?? '',
                        'work_place' => $userInfo['workplace']??'',
                        'remark' => $userInfo['remark']??'',
                        'order' => $userInfo['order'] ?? 0,
                        'is_admin' => $userInfo['isAdmin'] ? 1 : 0,
                        'is_boss' => $userInfo['isBoss'] ? 1 : 0,
                        'is_hide' => $userInfo['isHide'] ? 1 : 0,
                        'is_leader' => $userInfo['isLeader'] ? 1 : 0,
                        'name' => $userInfo['name'],
                        'active' => $userInfo['active'] ? 1 : 0,
                        'department' => join(',', $userInfo['department']),
                        'position' => $userInfo['position'] ?? '',
                        'email' => $userInfo['email'] ?? '',
                        'org_email' => $userInfo['orgEmail'] ?? '',
                        'avatar' => $userInfo['avatar'] ?? '',
                        'hired_date'=>empty($userInfo['hiredDate']) ? '0000-00-00 00:00:00' : date('Y-m-d', $userInfo['hiredDate'] / 1000),
                        'job_number' => $userInfo['jobnumber'] ?? '',
                        'ext_attr' => json_encode($userInfo['extattr']??new \stdClass(), 64 | 256),
                        'status' => 0
                    ];
                    if (!empty($relateId)) {
                        echo date('Y-m-d H:i:s') . "\t更新钉钉员工 {$userInfo['name']}[{$userInfo['userid']}]\n";
                        DingtalkDepartmentUser::updateAll($userParams, ['relate_id' => $relateId]);
                    } else {
                        echo date('Y-m-d H:i:s') . "\t新增钉钉员工 {$userInfo['name']}[{$userInfo['userid']}]\n";
                        DingtalkDepartmentUser::add($userParams);
                    }
                }
            }
        }

        //del others
        if(!empty($allDepartmentUserIndex)){
            $delRelateIds = array_values($allDepartmentUserIndex);
            DingtalkDepartmentUser::updateAll(['status'=>1],['relate_id'=>$delRelateIds]);
        }
    }


    private function updateDingDepartmentPathName(){
        for($level =1 ; $level <= 10; $level++){
            if($level==1){
                $sql = "update dingtalk_department set path_name=alias_name where level ={$level} and status=0;";
            }else{
                $sql = <<<SQL
update  dingtalk_department s
left join dingtalk_department p on s.parentid = p.id
set s.path_name = concat(p.path_name,'/',s.alias_name)
where s.status = 0 and p.status=0 and s.level={$level};
SQL;
            }

            DingtalkDepartment::getDb()->createCommand($sql)->execute();
        }

    }
}
