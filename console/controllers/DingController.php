<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use common\models\ehr\BusinessDepartment;
use common\models\ehr\DepartmentRelateToKael;
use common\models\ehr\DepartmentUser;
use common\models\UserCenter;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;


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
        echo date('Y-m-d H:i:s')."\t开始同步钉钉部门到kael\n";
        $this->updateDingDepartment();
        echo date('Y-m-d H:i:s')."\t部门同步结束\n";
        echo date('Y-m-d H:i:s')."\t开始同步钉钉人员到kael\n";
        $this->updateDingUser();
        echo date('Y-m-d H:i:s')."\t员工同步结束\n";

    }


    private function updateDingDepartment(){
        $allDepartmentList = DingTalkApi::getDepartmentAllList();
        $allIds = array_column($allDepartmentList,'id');
        $oldDepartmentIds = DingtalkDepartment::find()->select('id')->where(['status'=>0])->asArray(true)->column();
        $oldDepartmentIds = array_map('intval',$oldDepartmentIds);
        $delIds = array_diff($oldDepartmentIds,$allIds);
        $insertIds  = array_diff($allIds,$oldDepartmentIds);
        echo date('Y-m-d H:i:s')."\t新增部门如下:\n";
        echo json_encode($insertIds)."\n";
        echo date('Y-m-d H:i:s')."\t需要删除部门如下:\n";
        echo json_encode($delIds)."\n";
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
        $allUserIds = array_column(DingtalkUser::findList([],'','user_id'),'user_id');
        $newAllUserIds = [];
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
                    if(!in_array($userId,$newAllUserIds)){
                        $newAllUserIds[] = $userId;
                    }
                    $currentUserIds[] = $userId;
                    $userInfo = DingTalkApi::getUserInfo($userId);
                    echo "***************************************************************\n";
                    echo json_encode($userInfo)."\n";
                    if(in_array($userId,$allUserIds)){
                        echo date('Y-m-d H:i:s')."\t更新员工:\t";
                        echo $userInfo['userid']."\n";
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

                        //更新kael
                        $kaelId = DingtalkUser::findOneByWhere(['user_id'=>$userInfo['userid']],'kael_id');
                        $uid = $kaelId = $kaelId['kael_id'];
                        $user = UserCenter::findOne($kaelId);
                        if(!empty($user)){
                            $where = [];
                            if($user['username'] != $userInfo['name']){
                                $where['username'] = $userInfo['name'];
                            }
                            if($user['work_number'] != $userInfo['jobnumber']){
                                $where['work_number'] = $userInfo['jobnumber'];
                            }
                            if(isset($userInfo['mobile']) && $user['mobile'] != $userInfo['mobile']){
                                $where['mobile'] = $userInfo['mobile'];
                            }
                            if(isset($userInfo['email']) && $user['email'] != $userInfo['email']){
                                $where['email'] = $userInfo['email'];
                            }
                            if(!empty($where)){
                                UserCenter::updateAll($where,['id'=>$uid]);
                            }
                        }else{
                            echo date('Y-m-d H:i:s')."\t 钉钉账号:".$userInfo['userid']."\t没有关联kael账号\n";
                            //新增kael
                            $params = [
                                'username'=>$userInfo['name'],
                                'password'=>md5('1!Aaaaaaa'),
                                'sex'=>1,
                                'work_number'=>$userInfo['jobnumber'],
                                'mobile'=>isset($userInfo['mobile'])?$userInfo['mobile']:'',
                                'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                            ];
                            $uid = UserCenter::addUser($params);
                            //更新钉钉员工关联kael编号
                            DingtalkUser::updateAll(['kael_id'=>$uid],['user_id'=>$userInfo['userid']]);
                        }
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department'])?json_decode($userInfo['department'],true):$userInfo['department'];
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $oldDepartments = DepartmentUser::findList(['user_id'=>$uid],'depart_id');
                        $oldDepartmentIds = array_keys($oldDepartments);
                        $addDepartmentIds = array_diff($departmentIds,$oldDepartmentIds);
                        $deleteDepartmentIds = array_diff($oldDepartmentIds,$departmentIds);
                        if(!empty($addDepartmentIds)){
                            $cloumns = ['user_id','depart_id','is_leader','disp'];
                            $rows = [];
                            foreach ($addDepartmentIds as $did){
                                $leader = $isLeaderInDepts[$did]==="true"?1:0;
                                $order = isset($orderInDepts[$did])?$orderInDepts[$did]:'';
                                if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$uid,'depart_id'=>$did])){
                                    $rows[] = [$uid,$did,$leader,$order];
                                    BusinessDepartment::updateAll(['main_leader_id'=>$uid,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }else{
                                    if($record['is_leader'] != $leader){
                                        DepartmentUser::updateAll(['is_leader'=>$leader],['id'=>$record['id']]);
                                        BusinessDepartment::updateAll(['main_leader_id'=>$uid,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                    }
                                    if($record['disp'] != $order){
                                        DepartmentUser::updateAll(['disp'=>$order],['id'=>$record['id']]);
                                    }
                                }
                            }
                            DepartmentUser::addAllWithColumnRow($cloumns,$rows);
                        }
                        if(!empty($deleteDepartmentIds)){
                            DepartmentUser::updateAll(['status'=>1],['user_id'=>$uid,'depart_id'=>$deleteDepartmentIds]);
                        }
                        foreach ($departmentIds as $did){
                            if(!in_array($did,$addDepartmentIds) && !in_array($did,$deleteDepartmentIds)){
                                $where = [];
                                $isLeader = $isLeaderInDepts[$did]?1:0;
                                if($isLeader != $oldDepartments[$did]['is_leader']){
                                    BusinessDepartment::updateAll(['main_leader_id'=>$uid,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                    $where['is_leader'] = $isLeader;
                                }
                                if(isset($orderInDepts[$did]) && isset($oldDepartments[$did]) && $orderInDepts[$did] != $oldDepartments[$did]['disp']){
                                    $where['disp'] = $orderInDepts[$did];
                                }
                                if(!empty($where)){
                                    DepartmentUser::updateAll($where,['id'=>$oldDepartments[$did]['id']]);
                                }
                            }
                        }
                        //更新员工关联kael部门
                        $relateKaelDepartments = DepartmentRelateToKael::findList(['department_id'=>$departmentIds]);
                        $relateKaelDepartmentsIndexById = array_column($relateKaelDepartments,'department_id');
                        if(!empty($relateKaelDepartments)){
                            $departmentId = max($relateKaelDepartmentsIndexById); //合适的实际部门
                            $kaelDepartmentId = DepartmentRelateToKael::findOneByWhere(['department_id'=>$departmentId],'kael_department_id');
                            $kaelDepartmentId = $kaelDepartmentId['kael_department_id'];
                            if($user['department_id'] != $kaelDepartmentId){
                                UserCenter::updateAll(['department_id'=>$kaelDepartmentId],['id'=>$uid]);
                                DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$uid,'depart_id'=>$departmentId]);
                            }
                        }else{
                            UserCenter::updateAll(['department_id'=>0],['id'=>$uid]);
                        }

                    }else{
                        echo date('Y-m-d H:i:s')."\t新增员工:\n";
                        echo json_encode($userInfo,true)."\n";
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

                        //新增kael
                        $params = [
                            'username'=>$userInfo['name'],
                            'password'=>md5('1!Aaaaaaa'),
                            'sex'=>1,
                            'work_number'=>$userInfo['jobnumber'],
                            'mobile'=>isset($userInfo['mobile'])?$userInfo['mobile']:'',
                            'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                        ];

                        $uid = UserCenter::addUser($params);
                        echo "新增kael账号:\t".$uid."\n";
                        //更新钉钉员工关联kael编号
                        DingtalkUser::updateAll(['kael_id'=>$uid],['user_id'=>$userInfo['userid']]);

                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department'])?json_decode($userInfo['department'],true):$userInfo['department'];
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $cloumns = ['user_id','depart_id','is_leader','disp'];
                        $rows = [];
                        foreach ($departmentIds as $did){
                            $leader = $isLeaderInDepts[$did]==="true"?1:0;
                            $order = isset($orderInDepts[$did])?$orderInDepts[$did]:'';
                            if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$uid,'depart_id'=>$did])){
                                $rows[] = [$uid,$did,$leader,$order];
                                BusinessDepartment::updateAll(['main_leader_id'=>$uid,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                            }else{
                                if($record['is_leader'] != $leader){
                                    DepartmentUser::updateAll(['is_leader'=>$leader],['id'=>$record['id']]);
                                    BusinessDepartment::updateAll(['main_leader_id'=>$uid,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }
                                if($record['disp'] != $order){
                                    DepartmentUser::updateAll(['disp'=>$order],['id'=>$record['id']]);
                                }
                            }
                        }
                        DepartmentUser::addAllWithColumnRow($cloumns,$rows);

                        //更新员工关联kael部门
                        $relateKaelDepartments = DepartmentRelateToKael::findList(['department_id'=>$departmentIds]);
                        $relateKaelDepartmentsIndexById = array_column($relateKaelDepartments,'department_id');
                        if(!empty($relateKaelDepartments)){
                            $departmentId = max($relateKaelDepartmentsIndexById); //合适的实际部门
                            $kaelDepartmentId = DepartmentRelateToKael::findOneByWhere(['department_id'=>$departmentId],'kael_department_id');
                            $kaelDepartmentId = $kaelDepartmentId['kael_department_id'];
                            UserCenter::updateAll(['department_id'=>$kaelDepartmentId],['id'=>$uid]);
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$uid,'depart_id'=>$departmentId]);
                        }
                    }
                }
            }
        }
        //同步删除员工
        $deleteUserIds = array_diff($allUserIds,$newAllUserIds);
        $deleteUids = array_keys(DingtalkUser::findList(['user_id'=>$deleteUserIds],'kael_id','kael_id'));
        echo date('Y-m-d H:i:s')."\t需要删除员工如下:\n";
        echo json_encode($deleteUids)."\n";
        if(!empty($deleteUids)){
            DingtalkUser::updateAll(['status'=>1],['user_id'=>$deleteUserIds]);
            UserCenter::updateAll(['status'=>1],['id'=>$deleteUids]);
            DepartmentUser::updateAll(['status'=>1],['user_id'=>$deleteUids]);
        }
    }

    private function convertJsonMapToArray($string){
        $list = [];
        $string = substr($string,1);
        $string = substr($string,0,strlen($string)-1);
        if(empty($string)){
            return $list;
        }
        $departmentSplit = explode(',',$string);
        foreach ($departmentSplit as $v){
            $tmp = explode(':',$v);
            $list[$tmp[0]] = $tmp[1];
        }
        return $list;
    }

    public function actionDingDepartment(){
        $allDepartmentList = DingTalkApi::getDepartmentAllList();
        $allIds = array_column($allDepartmentList,'id');
        $oldDepartmentIds = DingtalkDepartment::find()->select('id')->where(['status'=>0])->asArray(true)->column();
        $oldDepartmentIds = array_map('intval',$oldDepartmentIds);
        $delIds = array_diff($oldDepartmentIds,$allIds);
        $insertIds  = array_diff($allIds,$oldDepartmentIds);
        echo date('Y-m-d H:i:s')."\t新增部门如下:\n";
        echo json_encode($insertIds)."\n";
        echo date('Y-m-d H:i:s')."\t需要删除部门如下:\n";
        echo json_encode($delIds)."\n";
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
        echo "部门同步成功\n";
    }

    public function actionDingUser(){
        $allUserIds = array_column(DingtalkUser::findList([],'','user_id'),'user_id');
        $newAllUserIds = [];
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
                    if(!in_array($userId,$newAllUserIds)){
                        $newAllUserIds[] = $userId;
                    }
                    $currentUserIds[] = $userId;
                    $userInfo = DingTalkApi::getUserInfo($userId);
                    echo "***************************************************************\n";
                    echo json_encode($userInfo)."\n";
                    if(in_array($userId,$allUserIds)){
                        continue;
                        echo date('Y-m-d H:i:s')."\t更新员工:\t";
                        echo $userInfo['userid']."\n";
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
                        echo date('Y-m-d H:i:s')."\t新增员工:\n";
                        echo json_encode($userInfo,true)."\n";
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

    public function actionOldKaelAccountRelateToDingAccount(){
        echo "开始绑定kael账号到钉钉账号\n";
        $kaelAccounts = UserCenter::findList();
        foreach ($kaelAccounts as $v){
            if(!empty($v['mobile']) && $dingUser = DingtalkUser::findOneByWhere(['mobile'=>$v['mobile']])){
                DingtalkUser::updateAll(['kael_id'=>$v['id']],['user_id'=>$dingUser['user_id']]);
                echo "[手机号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$v['id']."\n";
            }elseif(!empty($v['username']) && !empty($v['work_number']) && $dingUser = DingtalkUser::findOneByWhere(['name'=>$v['username'],'job_number'=>$v['work_number']])){
                DingtalkUser::updateAll(['kael_id'=>$v['id']],['user_id'=>$dingUser['user_id']]);
                echo "[用户名+工号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$v['id']."\n";
            }
        }
        echo "绑定任务结束\n";
    }
}
