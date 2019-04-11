<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use common\models\ehr\BusinessDepartment;
use common\models\DepartmentRelateToKael;
use common\models\ehr\DepartmentUser;
use common\models\UserCenter;
use Yii;
use yii\console\Controller;

class DingController extends Controller
{
    //钉钉同步功能初始化流程

    //第一步 准备好数据库更新
    //第二步 [ehr] kael部门关联钉钉部门
    //第三步 sql执行（超哥执行）
    //第四步 手动开启 ding/update 定时任务


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
        !empty($delIds) && DingtalkDepartment::updateAll(['status'=>1],['id'=>$delIds]);
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
        $i = 0;
        for($level = 1; $level <= 10 ;$level ++){
            $departmentList = DingtalkDepartment::find()->where(['status'=>0,'level'=>$level])
                ->asArray(true)->all();
            foreach ($departmentList as $v) {
                $userIdList = DingTalkApi::getDepartmentUserIds($v['id']);
                foreach ($userIdList as $userId){
                    if($userId != '00153'){    //测试 账号
                        continue;
                    }
                    if(in_array($userId,$currentUserIds)){
                        continue;
                    }
                    echo "\n****\t第".$i."次执行\t****\n";
                    $i++;
                    if(!in_array($userId,$newAllUserIds)){
                        $newAllUserIds[] = $userId;
                    }
                    $currentUserIds[] = $userId;
                    $userInfo = DingTalkApi::getUserInfo($userId);
                    echo "\n\n\n\n\n***************************************************************\n\n\n";
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
                                'department_id'=>$userInfo['department'][0], //@todo modify main-department
                                'department_subroot'=>$departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                                'status'=>0
                            ],
                            ['user_id'=>$userInfo['userid']]);

                        //更新kael @todo rename
                        $kaelInfo = DingtalkUser::findOneByWhere(['user_id'=>$userInfo['userid']],'kael_id');
                        $kaelId = $kaelInfo['kael_id'];
                        $user = UserCenter::findOne($kaelId);
                        if(!empty($user)){
                            //@todo rename
                            $params = [];
                            if($user['username'] != $userInfo['name']){
                                $params['username'] = $userInfo['name'];
                            }
                            if($user['work_number'] != $userInfo['jobnumber']){
                                $params['work_number'] = $userInfo['jobnumber'];
                            }
                            if(isset($userInfo['mobile']) && $user['mobile'] != $userInfo['mobile']){
                                $params['mobile'] = $userInfo['mobile'];
                            }
//                            if(isset($userInfo['email']) && $user['email'] != $userInfo['email']){
//                                $where['email'] = $userInfo['email'];
//                            }
                            if(!empty($params)){
                                UserCenter::updateAll($params,['id'=>$kaelId]);
                            }
                        }else{
                            if(!empty($userInfo['mobile'])){
                                if($user = UserCenter::findOneByWhere(['mobile'=>$userInfo['mobile']])){
                                    $kaelId = $user['id'];
                                    //更新钉钉员工关联kael编号
                                    DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                    echo "[手机号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                                }

                            }
                            if(!$user && !empty($userInfo['work_number'])){
                                if($user = UserCenter::findOneByWhere(['work_number'=>$userInfo['jobnumber']])){
                                    $kaelId = $user['id'];
                                    //更新钉钉员工关联kael编号
                                    DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                    echo "[工号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                                }
                            }
                            if(!$user){
                                echo date('Y-m-d H:i:s')."\t 钉钉账号:".$userInfo['userid']."\t没有关联kael账号\n";
                                //新增kael
                                $params = [
                                    'username'=>$userInfo['name'],
                                    'password'=>md5('1!Aaaaaaa'),
                                    'sex'=>1,
                                    'work_number'=>$userInfo['jobnumber'],
                                    'mobile'=>$userInfo['mobile']??'',
                                    'email'=>$userInfo['email']??'',
                                ];
                                $kaelId = UserCenter::addUser($params);
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                $user = UserCenter::findOne($kaelId);
                            }
                        }
                        //更新实际部门相关  @todo main-department upupup
                        $departmentIds = !is_array($userInfo['department'])?json_decode($userInfo['department'],true):$userInfo['department'];
                        //update #todo update dinktalk_department-----  lead
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        //@todo userinfo->department_id

                        $oldDepartments = DepartmentUser::findList(['user_id'=>$kaelId],'depart_id');
                        $oldDepartmentIds = array_keys($oldDepartments);
                        $addDepartmentIds = array_diff($departmentIds,$oldDepartmentIds);
                        $deleteDepartmentIds = array_diff($oldDepartmentIds,$departmentIds);
                        //新增用户关联部门
                        if(!empty($addDepartmentIds)){
                            $cloumns = ['user_id','depart_id','is_leader','disp'];
                            $rows = [];
                            foreach ($addDepartmentIds as $did){
                                $leader = $isLeaderInDepts[$did]==="true"?1:0;
                                $order = isset($orderInDepts[$did])?$orderInDepts[$did]:'';

                                //更新部门用户关系表
                                if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$kaelId,'depart_id'=>$did])){
                                    $rows[] = [$kaelId,$did,$leader,$order];
                                    BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }else{
                                    if($record['is_leader'] != $leader){
                                        DepartmentUser::updateAll(['is_leader'=>$leader],['id'=>$record['id']]);
                                        BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                    }
                                    if($record['disp'] != $order){
                                        DepartmentUser::updateAll(['disp'=>$order],['id'=>$record['id']]);
                                    }
                                }

                                //更新钉钉部门表 部门领导人
                                if($leader){
                                    $dingDepartment = DingtalkDepartment::findOneByWhere(['id'=>$did]);
                                    if($dingDepartment['main_leader_id'] != $kaelId){
                                        DingtalkDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['id'=>$did]);
                                    }
                                }
                            }
                            DepartmentUser::addAllWithColumnRow($cloumns,$rows);
                        }
                        //删除旧关联部门
                        if(!empty($deleteDepartmentIds)){
                            DepartmentUser::updateAll(['status'=>1],['user_id'=>$kaelId,'depart_id'=>$deleteDepartmentIds]);
                        }
                        $founder = false;
                        //更新员工加入的部门
                        foreach ($departmentIds as $did){
                            if($did == 1) $founder = true;
                            if(!in_array($did,$addDepartmentIds) && !in_array($did,$deleteDepartmentIds)){
                                $params = [];
                                $isLeader = $isLeaderInDepts[$did]?1:0;
                                if($isLeader != $oldDepartments[$did]['is_leader']){
                                    BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                    $params['is_leader'] = $isLeader;
                                }
                                if(isset($orderInDepts[$did]) && isset($oldDepartments[$did]) && $orderInDepts[$did] != $oldDepartments[$did]['disp']){
                                    $params['disp'] = $orderInDepts[$did];
                                }
                                if(!empty($params)){
                                    DepartmentUser::updateAll($params,['id'=>$oldDepartments[$did]['id']]);
                                }
                            }
                        }
                        //更新员工关联kael部门  @todo 从主department_id开始向父级依次匹配
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main'=>1,'depart_id'=>$departmentIds])->scalar();
                        if(!$mainDingDepartmentForUser && !empty($departmentIds)){
                            $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                        }
                        print_r($departmentIds);
                        echo $mainDingDepartmentForUser."#\n";
                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        echo $relateKaelDepartmentId."#\n";
                        exit('@@@@');
                        if($relateKaelDepartmentId && !$founder){
                            UserCenter::updateAll(['department_id'=>$relateKaelDepartmentId],['id'=>$kaelId]);
                        }elseif(!$founder){
                            UserCenter::updateAll(['department_id'=>151],['id'=>$kaelId]);
                        }

                    }else{
                        // 新增
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

                        if(!empty($userInfo['mobile'])){
                            if($user = UserCenter::findOneByWhere(['mobile'=>$userInfo['mobile']])){
                                $kaelId = $user['id'];
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                echo "[手机号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                            }

                        }
                        if(!$user && !empty($userInfo['work_number'])){
                            if($user = UserCenter::findOneByWhere(['work_number'=>$userInfo['jobnumber']])){
                                $kaelId = $user['id'];
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                echo "[工号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                            }
                        }
                        if(!$user){
                            //新增kael
                            $params = [
                                'username'=>$userInfo['name'],
                                'password'=>md5('1!Aaaaaaa'),
                                'sex'=>1,
                                'work_number'=>$userInfo['jobnumber'],
                                'mobile'=>isset($userInfo['mobile'])?$userInfo['mobile']:'',
                                'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                            ];

                            $kaelId = UserCenter::addUser($params);
                            echo "新增kael账号:\t".$kaelId."\n";
                        }
                        //更新钉钉员工关联kael编号
                        DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);

                        $founder = false;
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department'])?json_decode($userInfo['department'],true):$userInfo['department'];
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $cloumns = ['user_id','depart_id','is_leader','disp'];
                        $rows = [];
                        foreach ($departmentIds as $did){
                            if($did==1) $founder = true;
                            $leader = $isLeaderInDepts[$did]==="true"?1:0;
                            $order = isset($orderInDepts[$did])?$orderInDepts[$did]:'';
                            if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$kaelId,'depart_id'=>$did])){
                                $rows[] = [$kaelId,$did,$leader,$order];
                                BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                            }else{
                                if($record['is_leader'] != $leader){
                                    DepartmentUser::updateAll(['is_leader'=>$leader],['id'=>$record['id']]);
                                    BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }
                                if($record['disp'] != $order){
                                    DepartmentUser::updateAll(['disp'=>$order],['id'=>$record['id']]);
                                }
                            }
                            //更新钉钉部门表 部门领导人
                            if($leader){
                                $dingDepartment = DingtalkDepartment::findOneByWhere(['id'=>$did]);
                                if($dingDepartment['main_leader_id'] != $kaelId){
                                    DingtalkDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['id'=>$did]);
                                }
                            }
                        }
                        DepartmentUser::addAllWithColumnRow($cloumns,$rows);

                        //更新员工关联kael部门
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main'=>1,'depart_id'=>$departmentIds])->scalar();
                        if(!$mainDingDepartmentForUser && !empty($departmentIds)){
                            $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                        }
                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        if($relateKaelDepartmentId && !$founder){
                            UserCenter::updateAll(['department_id'=>$relateKaelDepartmentId],['id'=>$kaelId]);
                        }elseif(!$founder){
                            UserCenter::updateAll(['department_id'=>151],['id'=>$kaelId]);
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

    private function getRelateKaelDepartment($dingDepartmentId){
        static $departId;
        $relateKaelDepartment = DepartmentRelateToKael::findOneByWhere(['department_id'=>$dingDepartmentId],'kael_department_id');
        if(!empty($relateKaelDepartment)){
            $departId = $relateKaelDepartment['kael_department_id'];
        }elseif($parentDepartment = DingtalkDepartment::findOneByWhere(['id'=>$dingDepartmentId],'parentid')){
            if($parentDepartment['parentid'] && $parentDepartment['parentid'] != 1){
                self::getRelateKaelDepartment($parentDepartment['parentid']);
            }
        }
        return $departId;
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


//    public function actionOldKaelAccountRelateToDingAccount(){
//        echo "开始绑定kael账号到钉钉账号\n";
//        $kaelAccounts = UserCenter::findList();
//        foreach ($kaelAccounts as $v){
//            if(!empty($v['mobile']) && $dingUser = DingtalkUser::findOneByWhere(['mobile'=>$v['mobile']])){
//                DingtalkUser::updateAll(['kael_id'=>$v['id']],['user_id'=>$dingUser['user_id']]);
//                echo "[手机号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$v['id']."\n";
//            }elseif(!empty($v['username']) && !empty($v['work_number']) && $dingUser = DingtalkUser::findOneByWhere(['name'=>$v['username'],'job_number'=>$v['work_number']])){
//                DingtalkUser::updateAll(['kael_id'=>$v['id']],['user_id'=>$dingUser['user_id']]);
//                echo "[用户名+工号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$v['id']."\n";
//            }
//        }
//        echo "绑定任务结束\n";
//    }
}
