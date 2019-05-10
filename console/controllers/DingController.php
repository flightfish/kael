<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\libs\EmailApi;
use common\models\db\EmailRecord;
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
//                if($v['id'] != '111705883'){   //测试
//                    continue;
//                }
                $userIdList = DingTalkApi::getDepartmentUserIds($v['id']);
                echo "#####################################\t开始部门用户同步任务\n";
                echo "#####\t".date('Y-m-d H:i:s')."\t钉钉部门：".$v['name']."[".$v['id']."]"."\n";
                echo "#####\t".json_encode($userIdList)."\n";
                echo "#####################################\n";
                foreach ($userIdList as $userId){
//                    if($userId != '00153'){    //测试 账号
//                        continue;
//                    }
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
//                    echo json_encode($userInfo)."\n";

                    if(!in_array($userId,$allUserIds)){
                        $dingUser = DingtalkUser::findOneByWhere(['user_id'=>$userId],'','',-1);
                        if(isset($dingUser['status']) && $dingUser['status']){
                            $allUserIds[] = $userId;
                            if($dingUser['kael_id']){
                                UserCenter::updateAll(['status'=>0],['id'=>$dingUser['kael_id']]);
                            }
                        }
                    }
                    if(in_array($userId,$allUserIds)){
                        echo date('Y-m-d H:i:s')."\t更新员工:\t";
                        echo $userInfo['userid']."\n";
                        //更新
                        $updateParams = [
                            'name'=>$userInfo['name'],
//                            'email'=>$userInfo['email'] ?? "",
                            'mobile'=>$userInfo['mobile'],
                            'avatar'=>$userInfo['avatar'],
                            'job_number'=>$userInfo['jobnumber'],
                            'union_id'=>$userInfo['unionid'],
                            'open_id'=>$userInfo['openId'],
                            'departments'=>join(',',$userInfo['department']),
                            'department_id'=>$userInfo['department'][0], //@todo modify main-department
                            'department_subroot'=>$departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                            'status'=>0
                        ];
                        if(isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])){
                            $updateParams['hired_date'] = date('Y-m-d',$userInfo['hiredDate']/1000);
                        }
                        DingtalkUser::updateAll($updateParams,['user_id'=>$userInfo['userid']]);

                        //更新kael @todo rename
                        $dingTalkUser = DingtalkUser::findOneByWhere(['user_id'=>$userInfo['userid']],'kael_id,email');
                        $kaelId = $dingTalkUser['kael_id'];
                        $user = UserCenter::findOneByWhere(['id'=>$kaelId]);
                        if(!empty($user)){
                            $params = [];
                            if($user['username'] != $userInfo['name']){
                                $params['username'] = $userInfo['name'];
                            }
                            if($user['work_number'] != $userInfo['jobnumber']){
                                $params['work_number'] = $userInfo['jobnumber'];
                            }
                            if($user['user_type']){
                                $params['user_type'] = 0;
                            }
                            if(isset($userInfo['mobile']) && $user['mobile'] != $userInfo['mobile']){
                                $params['mobile'] = $userInfo['mobile'];
                            }
                            if($dingTalkUser['email'] != $user['email']){
                                $params['email'] = $dingTalkUser['email'];
                            }
                            if(!empty($userInfo['email']) || $dingTalkUser['email'] != $userInfo['email']){
                                DingTalkApi::updateEmailForUser($userInfo['userid'],$dingTalkUser['email']);
                                $params['email'] = $dingTalkUser['email'];
                            }
                            if(!empty($params)){
                                UserCenter::updateAll($params,['id'=>$kaelId]);
                            }
                        }else{
                            if(!empty($userInfo['mobile'])){
                                if($user = UserCenter::findOneByWhere(['mobile'=>$userInfo['mobile']])){
                                    if($user['user_type']){
                                        UserCenter::updateAll(['user_type'=>0],['id'=>$user['id']]);
                                    }
                                    $kaelId = $user['id'];
                                    //更新钉钉员工关联kael编号
                                    DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                    echo "[手机号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                                }

                            }
                            if(!$user && !empty($userInfo['jobnumber'])){
                                if($user = UserCenter::findOneByWhere(['work_number'=>$userInfo['jobnumber']])){
                                    if($user['user_type']){
                                        UserCenter::updateAll(['user_type'=>0],['id'=>$user['id']]);
                                    }
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
//                                    'email'=>$userInfo['email']??'',
                                    'user_type'=>0
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
                                if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$kaelId,'depart_id'=>$did],'','',-1)){
                                    $rows[] = [$kaelId,$did,$leader,$order];
                                    BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }else{
                                    $relateUpdateParams = [];
                                    if($record['status']){
                                        $relateUpdateParams['status'] = 0;
                                    }
                                    if($record['is_leader'] != $leader){
                                        $relateUpdateParams['is_leader'] = $leader;
                                        BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                    }
                                    if($record['disp'] != $order){
                                        $relateUpdateParams['disp'] = $order;
                                    }
                                    if(!empty($relateUpdateParams)){
                                        DepartmentUser::updateAll($relateUpdateParams,['id'=>$record['id']]);
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
                                $isLeader = $isLeaderInDepts[$did]==='true'?1:0;
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
                                if($isLeader){
                                    $dingDepartment = DingtalkDepartment::findOneByWhere(['id'=>$did]);
                                    if($dingDepartment['main_leader_id'] != $kaelId || $dingDepartment['main_leader_name'] != $userInfo['name']){
                                        DingtalkDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['id'=>$did]);
                                    }
                                }
                            }
                        }
                        //更新员工关联kael部门  @todo 从主department_id开始向父级依次匹配
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main'=>1,'user_id'=>$kaelId])->scalar();
                         if(!$mainDingDepartmentForUser && !empty($departmentIds)){ //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                             DingtalkUser::updateAll(['department_id'=>$mainDingDepartmentForUser],['user_id'=>$userInfo['userid']]);
                         }elseif($mainDingDepartmentForUser && !in_array($mainDingDepartmentForUser,$departmentIds) && !empty($departmentIds)){
                            DepartmentUser::updateAll(['is_main'=>0],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                             $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                             DingtalkUser::updateAll(['department_id'=>$mainDingDepartmentForUser],['user_id'=>$userInfo['userid']]);
                         }

                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
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
                        $addParams = [
                            'user_id'=>$userInfo['userid'],
                            'name'=>$userInfo['name'],
//                            'email'=>$userInfo['email'] ?? "",
                            'mobile'=>$userInfo['mobile'],
                            'avatar'=>$userInfo['avatar'],
                            'job_number'=>$userInfo['jobnumber'],
                            'union_id'=>$userInfo['unionid'],
                            'open_id'=>$userInfo['openId'],
                            'departments'=>join(',',$userInfo['department']),
                            'department_id'=>$userInfo['department'][0],
                            'department_subroot'=>$departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                        ];
                        if(isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])){
                            $addParams['hired_date'] = date('Y-m-d',$userInfo['hiredDate']/1000);
                        }
                        DingtalkUser::add($addParams);
                        if(!empty($userInfo['mobile'])){
                            if($user = UserCenter::findOneByWhere(['mobile'=>$userInfo['mobile'],'user_type'=>0])){
                                if($user['user_type']){
                                    UserCenter::updateAll(['user_type'=>0],['id'=>$user['id']]);
                                }
                                $kaelId = $user['id'];
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$userInfo['userid']]);
                                echo "[手机号]钉钉账号:".$userInfo['userid']."\t->绑定->\tkael账号:".$user['id']."\n";
                            }

                        }
                        if(!$user && !empty($userInfo['jobnumber'])){
                            if($user = UserCenter::findOneByWhere(['work_number'=>$userInfo['jobnumber'],'user_type'=>0])){
                                if($user['user_type']){
                                    UserCenter::updateAll(['user_type'=>0],['id'=>$user['id']]);
                                }
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
//                                'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                                'user_type'=>0
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

                            //更新实际部门关系
                            if(!$record = DepartmentUser::findOneByWhere(['user_id'=>$kaelId,'depart_id'=>$did],'','',-1)){
                                $rows[] = [$kaelId,$did,$leader,$order];
                                BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                            }else{
                                $relateUpdateParams = [];
                                if($record['status']){
                                    $relateUpdateParams['status'] = 0;
                                }
                                if($record['is_leader'] != $leader){
                                    $relateUpdateParams['is_leader'] = $leader;
                                    BusinessDepartment::updateAll(['main_leader_id'=>$kaelId,'main_leader_name'=>$userInfo['name']],['depart_id'=>$did]);
                                }
                                if($record['disp'] != $order){
                                    $relateUpdateParams['disp'] = $order;
                                }
                                if(!empty($relateUpdateParams)){
                                    DepartmentUser::updateAll($relateUpdateParams,['id'=>$record['id']]);
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
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main'=>1,'user_id'=>$kaelId])->scalar();
                        if(!$mainDingDepartmentForUser && !empty($departmentIds)){ //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id'=>$mainDingDepartmentForUser],['user_id'=>$userInfo['userid']]);
                        }elseif($mainDingDepartmentForUser && !in_array($mainDingDepartmentForUser,$departmentIds) && !empty($departmentIds)){
                            DepartmentUser::updateAll(['is_main'=>0],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                            $mainDingDepartmentForUser = $departmentIds[0];
                            DepartmentUser::updateAll(['is_main'=>1],['user_id'=>$kaelId,'depart_id'=>$mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id'=>$mainDingDepartmentForUser],['user_id'=>$userInfo['userid']]);
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

        //根据钉钉变动同步删除钉钉用户及kael用户
        $deleteUserIds = array_diff($allUserIds,$newAllUserIds);
        $deleteUids = array_keys(DingtalkUser::findList(['user_id'=>$deleteUserIds],'kael_id','kael_id'));
        echo date('Y-m-d H:i:s')."\t需要删除员工如下:\n";
        echo json_encode($deleteUids)."\n";
        if(!empty($deleteUids)){
            DingtalkUser::updateAll(['status'=>1],['user_id'=>$deleteUserIds]);
            UserCenter::updateAll(['status'=>1],['id'=>$deleteUids]);
            DepartmentUser::updateAll(['status'=>1],['user_id'=>$deleteUids]);
        }
        //全局更新后根据钉钉全局结果同步删除掉kael用户(可能由于历史原因造成kael用户冗余,所以执行该部分)
//        $kaelIds = array_keys(DingtalkUser::findList([],'kael_id','kael_id'));
//        $deleteKaelIds = array_column(UserCenter::findListByWhereAndWhereArr([],[['not in','id',$kaelIds]],'id'),'id');
//        if(!empty($deleteKaelIds)){
//            UserCenter::updateAll(['status'=>1],['id'=>$deleteKaelIds]);
//            DepartmentUser::updateAll(['status'=>1],['user_id'=>$deleteKaelIds]);
//        }
    }

    private function getRelateKaelDepartment($dingDepartmentId,$i=0){
        static $departId;
        if(!$i){
            $departId = null;
        }
        $relateKaelDepartment = DepartmentRelateToKael::findOneByWhere(['department_id'=>$dingDepartmentId],'kael_department_id');
        if(!empty($relateKaelDepartment)){
            $departId = $relateKaelDepartment['kael_department_id'];
        }elseif($parentDepartment = DingtalkDepartment::findOneByWhere(['id'=>$dingDepartmentId],'parentid')){
            if($parentDepartment['parentid'] && $parentDepartment['parentid'] != 1){
                $i++;
                self::getRelateKaelDepartment($parentDepartment['parentid'],$i);
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

    /**
     * 钉钉同步
     * 出生日期
     */
    public function actionBirthday(){
        if(exec('ps -ef|grep "ding/birthday"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        echo date('Y-m-d H:i:s')."\t*********************开始更新出生日期\n";
        $id = 0 ;
        while (1){
            if(! $dingUserList = DingtalkUser::findListByWhereWithWhereArr([],[['>','auto_id',$id]],'auto_id,user_id,name,birthday','auto_id asc',10)){
                break;
            }
            $dingUserInfos = array_column(DingTalkApi::getUserInfoForFieldsByUids(array_column($dingUserList,'user_id'),'sys02-birthTime'),null,'userid');
            foreach ($dingUserList as $v){
                if(isset($dingUserInfos[$v['user_id']])){
                    $fieldList = array_column($dingUserInfos[$v['user_id']]['field_list'],null,'field_code');
                    if(isset($fieldList['sys02-birthTime']) && isset($fieldList['sys02-birthTime']['value']) && !empty($fieldList['sys02-birthTime']['value'])){
                        $birthday = $fieldList['sys02-birthTime']['value'];
                        $birthday = date('m-d',strtotime($birthday));
                        $birthday && DingtalkUser::updateAll(['birthday'=>$birthday],['user_id'=>$v['user_id']]);
                        echo "\n更新钉钉用户:".$v['name']."[".$v['user_id']."]"."\t"."出生日期为:".$birthday."\n\n";
                    }
                }
                $id = $v['auto_id'];
            }
        }
        echo date('Y-m-d H:i:s')."\t*********************更新结束\n";
    }
}
