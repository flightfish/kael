<?php
namespace console\controllers;

use common\libs\DingTalkApi;
use common\libs\DingTalkApiJZ;
use common\models\DBCommon;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use common\models\ehr\AuthUser;
use common\models\ehr\AuthUserRoleDataPermRecord;
use common\models\ehr\AuthUserRoleRecord;
use common\models\ehr\BusinessDepartment;
use common\models\DepartmentRelateToKael;
use common\models\ehr\BusinessLineRelateSecondLeader;
use common\models\ehr\BusinessLineRelateStaff;
use common\models\ehr\ConcernAnniversaryRecord;
use common\models\ehr\ConcernBirthdayRecord;
use common\models\ehr\DepartmentUser;
use common\models\ehr\PsAnswer;
use common\models\ehr\PsEvaluateRelate;
use common\models\ehr\PsMessageDetail;
use common\models\ehr\PushCenterAcceptUserRecord;
use common\models\ehr\PushCenterLog;
use common\models\ehr\StaffFieldEditRecord;
use common\models\TmpImportJianzhi;
use common\models\UserCenter;
use common\models\UserInfo;
use Yii;
use yii\console\Controller;

class DingController extends Controller
{

    public function actionUpdateJzUserTest(){
        echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉部门到kael\n";
        $this->updateDingDepartmentJZ();
        echo date('Y-m-d H:i:s')."\t部门同步兼职团队结束\n";

        echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉人员到kael\n";
        $this->updateDingUserJZ();
        echo date('Y-m-d H:i:s')."\t员工同步兼职团队结束\n";

    }


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

        echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉部门到kael\n";
        $this->updateDingDepartmentJZ();
        echo date('Y-m-d H:i:s')."\t部门同步兼职团队结束\n";


        echo date('Y-m-d H:i:s')."\t开始同步钉钉人员到kael\n";
        $this->updateDingUser();
        echo date('Y-m-d H:i:s')."\t员工同步结束\n";

        echo date('Y-m-d H:i:s')."\t开始同步兼职团队钉钉人员到kael\n";
        $this->updateDingUserJZ();
        echo date('Y-m-d H:i:s')."\t员工同步兼职团队结束\n";



    }


    private function updateDingDepartment(){
        $allDepartmentList = DingTalkApi::getDepartmentAllList();
        $allIds = array_column($allDepartmentList,'id');

        $oldDepartments = array_column(DingtalkDepartment::find()->select('*')->where(['status'=>0,'corp_type'=>1])->asArray(true)->all(),null,'id');
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
//                    $params['main_leader_id'] = 0;
//                    $params['main_leader_name'] = '';
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
    private function updateDingDepartmentJZ(){
        $allDepartmentList = DingTalkApiJZ::getDepartmentAllList();
        $allIds = array_column($allDepartmentList,'id');
        DingtalkDepartment::updateAll(['corp_type'=>2,'status'=>0],['id'=>$allIds]);
        $oldDepartments = array_column(DingtalkDepartment::find()->select('*')->where(['status'=>0,'corp_type'=>2])->asArray(true)->all(),null,'id');
        $oldDepartmentIds = array_keys($oldDepartments);
        $oldDepartmentIds = array_map('intval',$oldDepartmentIds);


        $delIds = array_diff($oldDepartmentIds,$allIds);
        $insertIds  = array_diff($allIds,$oldDepartmentIds);

        echo date('Y-m-d H:i:s')."\t新增兼职部门如下:\n";
        echo json_encode($insertIds)."\n";
        echo date('Y-m-d H:i:s')."\t需要删除兼职部门如下:\n";
        echo json_encode($delIds)."\n";
        $columns = ['id','name','alias_name','parentid','corp_type'];
        $rows = [];
        foreach ($allDepartmentList as $v){
            if(in_array($v['id'],$oldDepartmentIds)){
                $params = ['name'=>$v['name'],'parentid'=>$v['parentid']];
//                if(empty($oldDepartments[$v['id']]['alias_name'])){
                    $params['alias_name'] = $v['name'];
//                }

//                if($oldDepartments[$v['id']]['main_leader_id'] && ! $user = DingtalkUser::findOneByWhere(['kael_id'=>$oldDepartments[$v['id']]['main_leader_id']])){
//                    echo date('Y-m-d H:i:s')."\t部门负责人离职,重置为空,部门编号:".$v['id']."\t原部门负责人编号:".$oldDepartments[$v['id']]['main_leader_id']."\n";
//                    $params['main_leader_id'] = 0;
//                    $params['main_leader_name'] = '';
//                }
                DingtalkDepartment::updateAll($params,['id'=>$v['id']]);
            }elseif(in_array($v['id'],$insertIds)){
                $rows[] = [$v['id'],$v['name'],$v['name'],$v['parentid'],2];
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

    private function updateDingUser()
    {
        $corpType = 1;
        //所有企业员工
        echo 1;
        $allUserIds = array_column(DingtalkUser::findList(['corp_type'=>$corpType], '', 'user_id'), 'user_id');
        echo 2;
        $newAllUserIds = [];//所有
        $currentUserIds = [];//已经出现在过循环中
        $departmentToSubRoot = DingtalkDepartment::find()
            ->select('id,subroot_id')
            ->where(['status' => 0,'corp_type'=>$corpType])
            ->asArray(true)->all();
        echo "3\n";
        $departmentToSubRoot = array_column($departmentToSubRoot, 'subroot_id', 'id');
        $i = 0;
        for ($level = 0; $level < 10; $level++) {
            echo "level: ".$level."\n";
            if($level == 0){
                $departmentList = [
                    ['id'=>1,'name'=>'小盒科技']
                ];
            }else{
                $departmentList = DingtalkDepartment::find()->where(['status' => 0, 'level' => $level,'corp_type'=>$corpType])->orderBy('id')
                    ->asArray(true)->all();
            }
            foreach ($departmentList as $v) {
                echo "dept: {$v['id']}\n";
                $userIdList = DingTalkApi::getDepartmentUserIds($v['id']);
                echo date('Y-m-d H:i:s') . "\t同步部门人员：{$v['name']}[{$v['id']}]\n";
                echo json_encode($userIdList) . "\n";
                foreach ($userIdList as $userId) {
                    if (in_array($userId, $currentUserIds)) {
                        echo "{$userId} 在 current 中\n";
                        //重复
                        continue;
                    }
                    echo "第" . $i . "次执行*******\n";
                    $i++;
                    if (!in_array($userId, $newAllUserIds)) {
                        $newAllUserIds[] = $userId;
                    }
                    $currentUserIds[] = $userId;

                    try {
                        $userInfo = DingTalkApi::getUserInfo($userId);
                    } catch (\Exception $e) {
                        echo date('Y-m-d H:i:s') . "error\t钉钉账号:" . $userId . "\t 接口错误[获取用户信息]:" . $e->getMessage() . "\n";
                        continue;
                    }

                    if (!isset($userInfo['jobnumber']) || !$userInfo['jobnumber']) {
                        $userInfo['jobnumber'] = 'NO_'.microtime(true);
//                        echo "员工:{$userInfo['name']}[{$userInfo['userid']}]没有工号" . "\n";
//                        continue;
                    }


                    if (!in_array($userId, $allUserIds)) {
                        //新的userid
                        $dingUser = DingtalkUser::findOneByWhere(['user_id' => $userId,'corp_type'=>$corpType], '', '', -1);
                        if (isset($dingUser['status']) && $dingUser['status']) {
                            //之前已删除的 恢复
                            $allUserIds[] = $userId;
                            if ($dingUser['kael_id']) {
                                UserCenter::updateAll(['status' => 0], ['id' => $dingUser['kael_id']]);
                            }
                        }
                    }

                    if (count($userInfo['department']) == 1) {
                        //只有一个部门
                        $mainDepartId = $userInfo['department'][0];
                    } elseif (in_array(1, $userInfo['department'])) {
                        $mainDepartId = 1;
                    } else {
                        try {
                            $mainDingDepartmentForUserInfo = DingTalkApi::getUserInfoForFieldsByUids($userInfo['userid'], 'sys00-mainDept');
                            //多部门的主部门
                            sleep(60);
                        } catch (\Exception $e) {
                            echo date('Y-m-d H:i:s') . "api_error\t钉钉账号:" . $userId . "\t 接口错误[智能人事获取花名册用户信息]:" . $e->getMessage() . "\n";
                            continue;
                        }
                        $mainDingDepartmentForUserInfo = array_column($mainDingDepartmentForUserInfo, null, 'userid');
                        $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list'] = array_column($mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list'], null, 'field_code');
                        $mainDepartId = $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list']['sys00-mainDeptId']['value'] < 0 ? 1 : $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list']['sys00-mainDeptId']['value'];
                    }

                    if (in_array($userId, $allUserIds)) {
                        //旧数据
                        echo date('Y-m-d H:i:s') . "\t更新钉钉员工 {$userInfo['name']}[{$userInfo['userid']}]\n";
                        //更新
                        $updateParams = [
                            'name' => $userInfo['name'],
                            'mobile' => $userInfo['mobile'],
                            'avatar' => $userInfo['avatar'],
                            'job_number' => $userInfo['jobnumber'],
                            'union_id' => $userInfo['unionid'],
                            'open_id' => $userInfo['openId'],
                            'departments' => join(',', $userInfo['department']),
                            'department_id' => $mainDepartId,
                            'department_subroot' => $departmentToSubRoot[$mainDepartId] ?? $mainDepartId,
                            'status' => 0
                        ];
                        (isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])) && $updateParams['hired_date'] = date('Y-m-d', $userInfo['hiredDate'] / 1000);
                        DingtalkUser::updateAll($updateParams, ['user_id' => $userInfo['userid']]);

                        //更新kael
                        $dingTalkUser = DingtalkUser::findOneByWhere(['user_id' => $userInfo['userid']], '');
                        $kaelId = $dingTalkUser['kael_id'];
                        $user = UserCenter::findOneByWhere(['id' => $kaelId], '', -1);
                        if (!empty($user)) {
                            //存在旧的
                            echo "更新kael账号[{$kaelId}]\n";
                            $params = [];
                            $user['username'] != $userInfo['name'] && $params['username'] = $userInfo['name'];
                            $user['work_number'] != $userInfo['jobnumber'] && $params['work_number'] = $userInfo['jobnumber'];
                            $user['user_type'] != 0 && $params['user_type'] = 0;
                            $user['status'] != 0 && $params['status'] = 0;
                            (isset($userInfo['mobile']) && $user['mobile'] != $userInfo['mobile']) && $params['mobile'] = $userInfo['mobile'];
                            $dingTalkUser['email'] != $user['email'] && $params['email'] = $dingTalkUser['email'];
                            if (\Yii::$app->params['env'] == 'prod') {
                                //返更新邮箱
                                if (empty($userInfo['email']) || $dingTalkUser['email'] != $userInfo['email']) {
                                    DingTalkApi::updateEmailForUser($userInfo['userid'], $dingTalkUser['email']);
                                    $params['email'] = $dingTalkUser['email'];
                                }
                            }
                            !empty($params) && UserCenter::updateAll($params, ['id' => $kaelId]);
                        } else {
                            //新的
                            if (!empty($userInfo['mobile'])) {
                                if ($user = UserCenter::findOneByWhere(['mobile' => $userInfo['mobile']], '', -1)) {
                                    //有kaelid
                                    $params = [];
                                    $user['user_type'] != 0 && $params['user_type'] = 0;
                                    $user['status'] !=0 && $params['status'] = 0;
                                    !empty($params) && UserCenter::updateAll($params, ['id' => $user['id']]);
                                    $kaelId = $user['id'];
                                    //更新钉钉员工关联kael编号
                                    DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                    echo "[手机号]钉钉账号: {$userInfo['mobile']}[{$userInfo['userid']}] 更新绑定 kael账号:{$user['id']}\n";
                                }
                            }
                            if (!$user) {
                                //没有kael
                                echo date('Y-m-d H:i:s') . "\t 钉钉账号:" . $userInfo['userid'] . "\t没有关联kael账号\n";
                                //新增kael
                                $params = [
                                    'username' => $userInfo['name'],
                                    'password' => md5('1!Aaaaaaa'),
                                    'sex' => 1,
                                    'work_number' => $userInfo['jobnumber'],
                                    'mobile' => $userInfo['mobile'] ?? '',
                                    'user_type' => 0
                                ];
                                $kaelId = UserCenter::addUser($params);
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                $user = UserCenter::findOne($kaelId);
                                echo "新增kael账号[" . $kaelId . "]{$userInfo['name']}[{$userInfo['userid']}]\n";
                            }
                        }
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department']) ? json_decode($userInfo['department'], true) : $userInfo['department'];
                        if ($dids = array_column(DingtalkDepartment::findListByWhereAndWhereArr(['main_leader_id' => $kaelId], [['not in', 'id', $departmentIds]], 'id'), 'id')) {
//                            DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['id' => $dids]);
                        }
                        //update
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $oldDepartments = DepartmentUser::findList(['user_id' => $kaelId], 'depart_id');
                        $oldDepartmentIds = array_keys($oldDepartments);
                        $addDepartmentIds = array_diff($departmentIds, $oldDepartmentIds);
                        $deleteDepartmentIds = array_diff($oldDepartmentIds, $departmentIds);
                        //新增用户关联部门
                        if (!empty($addDepartmentIds)) {
                            $cloumns = ['user_id', 'depart_id', 'is_leader', 'disp'];
                            $rows = [];
                            foreach ($addDepartmentIds as $did) {
                                $leader = $isLeaderInDepts[$did] === "true" ? 1 : 0;
                                $order = isset($orderInDepts[$did]) ? $orderInDepts[$did] : '';

                                //更新部门用户关系表
                                if (!$record = DepartmentUser::findOneByWhere(['user_id' => $kaelId, 'depart_id' => $did], '', '', -1)) {
                                    $rows[] = [$kaelId, $did, $leader, $order];
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                } else {
                                    $relateUpdateParams = [];
                                    if ($record['status']) {
                                        $relateUpdateParams['status'] = 0;
                                    }
                                    if ($record['is_leader'] != $leader) {
                                        $relateUpdateParams['is_leader'] = $leader;
//                                        BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                    }
                                    if ($record['disp'] != $order) {
                                        $relateUpdateParams['disp'] = $order;
                                    }
                                    if (!empty($relateUpdateParams)) {
                                        DepartmentUser::updateAll($relateUpdateParams, ['id' => $record['id']]);
                                    }
                                }

                                //更新钉钉部门表 部门领导人
                                if ($leader) {
                                    $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                    if ($dingDepartment['main_leader_id'] != $kaelId) {
//                                        DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                    }
                                }
                            }
                            DepartmentUser::addAllWithColumnRow($cloumns, $rows);
                        }
                        //删除旧关联部门
                        if (!empty($deleteDepartmentIds)) {
                            DepartmentUser::updateAll(['status' => 1], ['user_id' => $kaelId, 'depart_id' => $deleteDepartmentIds]);
//                            DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['main_leader_id' => $kaelId, 'id' => $deleteDepartmentIds]);
                        }
                        $founder = false;
                        //更新员工加入的部门
                        foreach ($departmentIds as $did) {
                            if ($did == 1) $founder = true;
                            if (!in_array($did, $addDepartmentIds) && !in_array($did, $deleteDepartmentIds)) {
                                $params = [];
                                $isLeader = $isLeaderInDepts[$did] === 'true' ? 1 : 0;
                                if ($isLeader != $oldDepartments[$did]['is_leader']) {
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                    $params['is_leader'] = $isLeader;
                                }
                                if (isset($orderInDepts[$did]) && isset($oldDepartments[$did]) && $orderInDepts[$did] != $oldDepartments[$did]['disp']) {
                                    $params['disp'] = $orderInDepts[$did];
                                }
                                if (!empty($params)) {
                                    DepartmentUser::updateAll($params, ['id' => $oldDepartments[$did]['id']]);
                                }
                                $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                if ($isLeader) {
                                    if ($dingDepartment['main_leader_id'] != $kaelId || $dingDepartment['main_leader_name'] != $userInfo['name']) {
//                                        DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                    }
                                } elseif ($dingDepartment['main_leader_id'] == $kaelId) {
//                                    echo $kaelId . "\t 不再是部门" . $did . "的负责人";
//                                    DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['main_leader_id' => $kaelId, 'id' => $did]);
                                }
                            }
                        }
                        //更新员工关联kael部门  从主department_id开始向父级依次匹配
                        $mainDingDepartmentToUserInfo = DepartmentUser::findOneByWhere(['is_main' => 1, 'user_id' => $kaelId, 'status' => 0]);
                        $mainDingDepartmentForUser = $mainDingDepartmentToUserInfo['depart_id'] ?? '';
                        if (!$mainDingDepartmentForUser && !empty($departmentIds)) { //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            $params = [];
                            $params['is_main'] = 1;
                            $oldMainDingDepartmentToUserInfo = DepartmentUser::findOneByWhere(['is_main' => 1, 'user_id' => $kaelId, 'status' => 1], '', 'create_time desc');
                            if ($oldMainDingDepartmentToUserInfo) {
                                $params['position_type_id'] = $oldMainDingDepartmentToUserInfo['position_type_id'];
                                $params['job_position_id'] = $oldMainDingDepartmentToUserInfo['job_position_id'];
                                $params['finance_position_id'] = $oldMainDingDepartmentToUserInfo['finance_position_id'];
                            }
                            DepartmentUser::updateAll($params, ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);

                        } elseif ( $mainDingDepartmentForUser && $mainDingDepartmentForUser != $mainDepartId && !in_array($mainDingDepartmentForUser, $departmentIds) && !empty($departmentIds) && in_array($mainDepartId, $departmentIds)) {
                            DepartmentUser::updateAll(['is_main' => 0], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            $params = [
                                'is_main' => 1,
                                'position_type_id' => $mainDingDepartmentToUserInfo['position_type_id'],
                                'job_position_id' => $mainDingDepartmentToUserInfo['job_position_id'],
                                'finance_position_id' => $mainDingDepartmentToUserInfo['finance_position_id']
                            ];
                            DepartmentUser::updateAll($params, ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        }

                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        if ($relateKaelDepartmentId && !$founder) {
                            UserCenter::updateAll(['department_id' => $relateKaelDepartmentId], ['id' => $kaelId]);
                        } elseif (!$founder) {
                            UserCenter::updateAll(['department_id' => 151], ['id' => $kaelId]);
                        }
                    } else {
                        // 新增
                        echo date('Y-m-d H:i:s') . "\t新增员工:\n";
                        echo json_encode($userInfo, true) . "\n";

                        //新增
                        $addParams = [
                            'user_id' => $userInfo['userid'],
                            'name' => $userInfo['name'],
                            'mobile' => $userInfo['mobile'],
                            'avatar' => $userInfo['avatar'],
                            'job_number' => $userInfo['jobnumber'],
                            'union_id' => $userInfo['unionid'],
                            'open_id' => $userInfo['openId'],
                            'departments' => join(',', $userInfo['department']),
                            'department_id' => $userInfo['department'][0],
                            'department_subroot' => $departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                            'corp_type' => $corpType,
                        ];
                        if (isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])) {
                            $addParams['hired_date'] = date('Y-m-d', $userInfo['hiredDate'] / 1000);
                        }
                        DingtalkUser::add($addParams);
                        if (!empty($userInfo['mobile'])) {
                            if ($user = UserCenter::findOneByWhere(['mobile' => $userInfo['mobile'], 'user_type' => 0], '', -1)) {
                                $kaelId = $user['id'];
                                $params = [];
                                if ($user['user_type']) {
                                    $params['user_type'] = 0;
                                }
                                if ($user['status']) {
                                    $params['status'] = 0;
                                }
                                !empty($params) && UserCenter::updateAll($params, ['id' => $user['id']]);
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                echo "[手机号]钉钉账号:" . $userInfo['userid'] . "\t->绑定->\tkael账号:" . $user['id'] . "\n";
                            }
                        }

                        if (!$user) {
                            //新增kael
                            $params = [
                                'username' => $userInfo['name'],
                                'password' => md5('1!Aaaaaaa'),
                                'sex' => 1,
                                'work_number' => $userInfo['jobnumber'],
                                'mobile' => isset($userInfo['mobile']) ? $userInfo['mobile'] : '',
                                'user_type' => 0
                            ];

                            $kaelId = UserCenter::addUser($params);
                            echo "新增kael账号:\t" . $kaelId . "\n";
                        }
                        //更新钉钉员工关联kael编号
                        DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);

                        $founder = false;
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department']) ? json_decode($userInfo['department'], true) : $userInfo['department'];
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $cloumns = ['user_id', 'depart_id', 'is_leader', 'disp'];
                        $rows = [];
                        foreach ($departmentIds as $did) {
                            if ($did == 1) $founder = true;
                            $leader = $isLeaderInDepts[$did] === "true" ? 1 : 0;
                            $order = isset($orderInDepts[$did]) ? $orderInDepts[$did] : '';

                            //更新实际部门关系
                            if (!$record = DepartmentUser::findOneByWhere(['user_id' => $kaelId, 'depart_id' => $did], '', '', -1)) {
                                $rows[] = [$kaelId, $did, $leader, $order];
//                                BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                            } else {
                                $relateUpdateParams = [];
                                if ($record['status']) {
                                    $relateUpdateParams['status'] = 0;
                                }
                                if ($record['is_leader'] != $leader) {
                                    $relateUpdateParams['is_leader'] = $leader;
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                }
                                if ($record['disp'] != $order) {
                                    $relateUpdateParams['disp'] = $order;
                                }
                                if (!empty($relateUpdateParams)) {
                                    DepartmentUser::updateAll($relateUpdateParams, ['id' => $record['id']]);
                                }
                            }

                            //更新钉钉部门表 部门领导人
                            if ($leader) {
                                $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                if ($dingDepartment['main_leader_id'] != $kaelId) {
//                                    DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                }
                            }
                        }
                        DepartmentUser::addAllWithColumnRow($cloumns, $rows);

                        //更新员工关联kael部门
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main' => 1, 'user_id' => $kaelId, 'status' => 0])->scalar();
                        if (!$mainDingDepartmentForUser && !empty($departmentIds)) { //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            DepartmentUser::updateAll(['is_main' => 1], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        } elseif ($mainDingDepartmentForUser && !in_array($mainDingDepartmentForUser, $departmentIds) && !empty($departmentIds)) {
                            DepartmentUser::updateAll(['is_main' => 0], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            DepartmentUser::updateAll(['is_main' => 1], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        }
                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        if ($relateKaelDepartmentId && !$founder) {
                            UserCenter::updateAll(['department_id' => $relateKaelDepartmentId], ['id' => $kaelId]);
                        } elseif (!$founder) {
                            UserCenter::updateAll(['department_id' => 151], ['id' => $kaelId]);
                        }
                    }
                }
            }
        }

        //new
        $deleteUserIds = array_diff($allUserIds,$newAllUserIds);
        if(empty($deleteUserIds)){
            return ;
        }
        $deleteKaelInfos = DingtalkUser::findList(['user_id'=>$deleteUserIds],'user_id','user_id,kael_id');
        $deleteUids = array_column($deleteKaelInfos,'kael_id');
        echo date('Y-m-d H:i:s')."\t需要删除员工如下:\n";
        echo json_encode($deleteUids)."\n";
        if(!empty($deleteUserIds)){
            foreach ($deleteUserIds as $userId){
                try{
                    $dingInfo = DingTalkApi::getUserInfo($userId);
                    continue;
                }catch (\Exception $e){
                    if($e->getMessage() != '[DING]找不到该用户'){
                        echo date('Y-m-d H:i:s')."\t[delete:no] user_id:".$userId."\t".$e->getMessage()."\n";
                        continue;
                    }
                }
                echo date('Y-m-d H:i:s')."\t[delete:yes] user_id:".$userId."\t".$e->getMessage()."\n";

                $kaelId = isset($deleteKaelInfos[$userId])?$deleteKaelInfos[$userId]['kael_id']:0;
                if(!$kaelId){
                    echo date('Y-m-d H:i:s')."\t[error:kael_id=0] user_id:".$userId."\t".$e->getMessage()."\n";
                    continue;
                }
                if(!UserCenter::findOneByWhere(['id'=>$kaelId],'',-1)){
                    echo date('Y-m-d H:i:s')."\t[error:kael account not find] user_id:".$userId."\t".$e->getMessage()."\n";
                    continue;
                }
                $transKael = DingtalkUser::getDb()->beginTransaction();
                $tranEhr = BusinessLineRelateStaff::getDb()->beginTransaction();
                try {
                    //钉钉表
                    DingtalkUser::updateAll(['status'=>1],['user_id'=>$userId]);
                    //用户表
                    UserCenter::updateAll(['status'=>1],['id'=>$kaelId]);
                    //部门关联表
                    DepartmentUser::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    //用户附属信息表
                    UserInfo::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    //钉钉部门主表
//                    DingtalkDepartment::updateAll(['main_leader_id'=>0,'main_leader_name'=>''],['main_leader_id'=>$kaelId]);
//                    BusinessDepartment::updateAll(['main_leader_id'=>0,'main_leader_name'=>''],['main_leader_id'=>$kaelId]);
                    //ehr表
                    AuthUser::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    AuthUserRoleDataPermRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    AuthUserRoleRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    BusinessLineRelateSecondLeader::updateAll(['status'=>1],['leader_id'=>$kaelId]);
                    BusinessLineRelateStaff::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    ConcernAnniversaryRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    ConcernBirthdayRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    PsAnswer::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PsAnswer::updateAll(['status'=>1],['be_evaluator_id'=>$kaelId]);
                    PsEvaluateRelate::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PsEvaluateRelate::updateAll(['status'=>1],['be_evaluator_id'=>$kaelId]);
                    PsMessageDetail::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PushCenterAcceptUserRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    PushCenterLog::updateAll(['status'=>1],['accept_user_id'=>$kaelId]);
                    StaffFieldEditRecord::updateAll(['status'=>1],['staff_id'=>$kaelId]);
                    $transKael->commit();
                    $tranEhr->commit();
                } catch (\Exception $e){
                    $transKael->rollBack();
                    $tranEhr->rollBack();
                    throw $e;
                }
            }
        }
    }

    private function updateDingUserJZ()
    {
        $corpType = 2;
        //所有企业员工
        $allMobileBox = array_column(DingtalkUser::findList(['corp_type'=>1], '', 'mobile'), 'mobile');
        $allUserIds = array_column(DingtalkUser::findList(['corp_type'=>$corpType], '', 'user_id'), 'user_id');
        $newAllUserIds = [];//所有
        $currentUserIds = [];//已经出现在过循环中
        $departmentToSubRoot = DingtalkDepartment::find()
            ->select('id,subroot_id')
            ->where(['status' => 0,'corp_type'=>$corpType])
            ->asArray(true)->all();

        $departmentToSubRoot = array_column($departmentToSubRoot, 'subroot_id', 'id');
        $i = 0;
        for ($level = 0; $level < 10; $level++) {
            if($level == 0){
                $departmentList = [
                    ['id'=>1,'name'=>'兼职辅导团队']
                ];
            }else{
                $departmentList = DingtalkDepartment::find()->where(['status' => 0, 'level' => $level,'corp_type'=>$corpType])->orderBy('id')
                    ->asArray(true)->all();
            }
            foreach ($departmentList as $v) {
//                $userInfoList = DingTalkApiJZ::getDepartmentUserInfoList($v['id']);
//                $userIdList = array_column($userInfoList,'userid');
                $userIdList = DingTalkApiJZ::getDepartmentUserIds($v['id']);
                echo date('Y-m-d H:i:s') . "\t同步部门人员：{$v['name']}[{$v['id']}]\n";
//                echo json_encode($userInfoList) . "\n";
                echo json_encode($userIdList) . "\n";
//                foreach ($userInfoList as $userInfo){
//                    $userId = $userInfo['userid'];
                foreach ($userIdList as $userId) {
                    if (in_array($userId, $currentUserIds)) {
                        //重复
                        continue;
                    }
                    echo "\n****\t第" . $i . "次执行\t****\n";
                    $i++;
                    if (!in_array($userId, $newAllUserIds)) {
                        $newAllUserIds[] = $userId;
                    }
                    $currentUserIds[] = $userId;

                    try {
                        $userInfo = DingTalkApiJZ::getUserInfo($userId);
                        if(in_array($userInfo['mobile'],$allMobileBox)){
                            echo "{$userInfo['mobile']} {$userInfo['name']} 主公司员工 跳过\n";
                            DingtalkUser::updateAll(['status'=>1],['corp_type'=>$corpType,'user_id'=>$userInfo['userid']]);
                            //主公司员工
                            continue;
                        }
                    } catch (\Exception $e) {
                        echo date('Y-m-d H:i:s') . "error\t钉钉账号:" . $userId . "\t 接口错误[获取用户信息]:" . $e->getMessage() . "\n";
                        continue;
                    }

//                    if (!isset($userInfo['jobnumber']) || !$userInfo['jobnumber']) {
                    $tmpImportJianzhi = TmpImportJianzhi::find()->select('id,work_number')->where(['mobile'=>$userInfo['mobile']])->asArray(true)->one();
                    if(empty($tmpImportJianzhi)){
                        $columnsTmpJz = ['mobile','name','ding_userid','ding_error'];
                        DBCommon::batchInsertAll(
                            TmpImportJianzhi::tableName(),
                            $columnsTmpJz,
                            [
                                [$userInfo['mobile'],$userInfo['name'],$userInfo['userid'],'OLD IMPORT']
                            ],
                            TmpImportJianzhi::getDb(),
                            'INSERT IGNORE'
                        );
                        $tmpImportJianzhi = TmpImportJianzhi::find()->select('id,work_number')->where(['mobile'=>$userInfo['mobile']])->asArray(true)->one();
                    }
                    $newJobNumber = self::tmpIdToWorknumber($tmpImportJianzhi['id']);
                    if(empty($tmpImportJianzhi['work_number'])){
                        TmpImportJianzhi::updateAll(['work_number'=>$newJobNumber],['id'=>$tmpImportJianzhi['id']]);
                    }
                    if(empty($userInfo['jobnumber']) || $userInfo['jobnumber']!=$newJobNumber){
                        $userInfo['jobnumber'] = $newJobNumber;
                        DingTalkApiJZ::updateUser($userInfo['userid'],['jobnumber'=>$userInfo['jobnumber']]);
                    }

//                        $userInfo['jobnumber'] = 'NO_'.microtime(true);
//                        echo "员工:{$userInfo['name']}[{$userInfo['userid']}]没有工号" . "\n";
//                        continue;

//                    }


                    if (!in_array($userId, $allUserIds)) {
                        //新的userid
                        $dingUser = DingtalkUser::findOneByWhere(['user_id' => $userId,'corp_type'=>$corpType], '', '', -1);
                        if (isset($dingUser['status']) && $dingUser['status']) {
                            //之前已删除的 恢复
                            $allUserIds[] = $userId;
                            if ($dingUser['kael_id']) {
                                UserCenter::updateAll(['status' => 0], ['id' => $dingUser['kael_id']]);
                            }
                        }
                    }

                    if (count($userInfo['department']) == 1) {
                        //只有一个部门
                        $mainDepartId = $userInfo['department'][0];
                    } elseif (in_array(1, $userInfo['department'])) {
                        $mainDepartId = 1;
                    } else {
                        $mainDepartId = $userInfo['department'][0];
//                        try {
//                            $mainDingDepartmentForUserInfo = DingTalkApiJZ::getUserInfoForFieldsByUids($userInfo['userid'], 'sys00-mainDept');
//                            //多部门的主部门
//                            sleep(60);
//                        } catch (\Exception $e) {
//                            echo date('Y-m-d H:i:s') . "api_error\t钉钉账号:" . $userId . "\t 接口错误[智能人事获取花名册用户信息]:" . $e->getMessage() . "\n";
//                            continue;
//                        }
//                        $mainDingDepartmentForUserInfo = array_column($mainDingDepartmentForUserInfo, null, 'userid');
//                        $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list'] = array_column($mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list'], null, 'field_code');
//                        $mainDepartId = $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list']['sys00-mainDeptId']['value'] < 0 ? 1 : $mainDingDepartmentForUserInfo[$userInfo['userid']]['field_list']['sys00-mainDeptId']['value'];
                    }

                    if (in_array($userId, $allUserIds)) {
                        //旧数据
                        echo date('Y-m-d H:i:s') . "\t更新钉钉员工 {$userInfo['name']}[{$userInfo['userid']}]\n";
                        //更新
                        $updateParams = [
                            'name' => $userInfo['name'],
                            'mobile' => $userInfo['mobile'],
                            'avatar' => $userInfo['avatar'],
                            'job_number' => $userInfo['jobnumber'],
                            'union_id' => $userInfo['unionid'],
                            'open_id' => $userInfo['openId'],
                            'departments' => join(',', $userInfo['department']),
                            'department_id' => $mainDepartId,
                            'department_subroot' => $departmentToSubRoot[$mainDepartId] ?? $mainDepartId,
                            'status' => 0
                        ];
                        (isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])) && $updateParams['hired_date'] = date('Y-m-d', $userInfo['hiredDate'] / 1000);
                        DingtalkUser::updateAll($updateParams, ['user_id' => $userInfo['userid']]);

                        //更新kael
                        $dingTalkUser = DingtalkUser::findOneByWhere(['user_id' => $userInfo['userid']], '');
                        $kaelId = $dingTalkUser['kael_id'];
                        $user = UserCenter::findOneByWhere(['id' => $kaelId], '', -1);
                        if (!empty($user)) {
                            //存在旧的
                            echo "更新kael账号[{$kaelId}]\n";
                            $params = [];
                            $user['username'] != $userInfo['name'] && $params['username'] = $userInfo['name'];
                            $user['work_number'] != $userInfo['jobnumber'] && $params['work_number'] = $userInfo['jobnumber'];
                            $user['user_type'] != 0 && $params['user_type'] = 0;
                            $user['status'] != 0 && $params['status'] = 0;
                            (isset($userInfo['mobile']) && $user['mobile'] != $userInfo['mobile']) && $params['mobile'] = $userInfo['mobile'];
                            $dingTalkUser['email'] != $user['email'] && $params['email'] = $dingTalkUser['email'];
                            if (\Yii::$app->params['env'] == 'prod') {
                                //返更新邮箱
                                //无邮箱 兼职
//                                if (empty($userInfo['email']) || $dingTalkUser['email'] != $userInfo['email']) {
//                                    DingTalkApiJZ::updateEmailForUser($userInfo['userid'], $dingTalkUser['email']);
//                                    $params['email'] = $dingTalkUser['email'];
//                                }
                            }
                            !empty($params) && UserCenter::updateAll($params, ['id' => $kaelId]);
                        } else {
                            //新的
                            if (!empty($userInfo['mobile'])) {
                                if ($user = UserCenter::findOneByWhere(['mobile' => $userInfo['mobile']], '', -1)) {
                                    //有kaelid
                                    $params = [];
                                    $user['user_type'] != 0 && $params['user_type'] = 0;
                                    $user['status'] !=0 && $params['status'] = 0;
                                    !empty($params) && UserCenter::updateAll($params, ['id' => $user['id']]);
                                    $kaelId = $user['id'];
                                    //更新钉钉员工关联kael编号
                                    DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                    echo "[手机号]钉钉账号: {$userInfo['mobile']}[{$userInfo['userid']}] 更新绑定 kael账号:{$user['id']}\n";
                                }
                            }
                            if (!$user) {
                                //没有kael
                                echo date('Y-m-d H:i:s') . "\t 钉钉账号:" . $userInfo['userid'] . "\t没有关联kael账号\n";
                                //新增kael
                                $params = [
                                    'username' => $userInfo['name'],
                                    'password' => md5('1!Aaaaaaa'),
                                    'sex' => 1,
                                    'work_number' => $userInfo['jobnumber'],
                                    'mobile' => $userInfo['mobile'] ?? '',
                                    'user_type' => 0
                                ];
                                $kaelId = UserCenter::addUser($params);
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                $user = UserCenter::findOne($kaelId);
                                echo "新增kael账号[" . $kaelId . "]{$userInfo['name']}[{$userInfo['userid']}]\n";
                            }
                        }
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department']) ? json_decode($userInfo['department'], true) : $userInfo['department'];
                        if ($dids = array_column(DingtalkDepartment::findListByWhereAndWhereArr(['main_leader_id' => $kaelId], [['not in', 'id', $departmentIds]], 'id'), 'id')) {
//                            DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['id' => $dids]);
                        }
                        //update
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $oldDepartments = DepartmentUser::findList(['user_id' => $kaelId], 'depart_id');
                        $oldDepartmentIds = array_keys($oldDepartments);
                        $addDepartmentIds = array_diff($departmentIds, $oldDepartmentIds);
                        $deleteDepartmentIds = array_diff($oldDepartmentIds, $departmentIds);
                        //新增用户关联部门
                        if (!empty($addDepartmentIds)) {
                            $cloumns = ['user_id', 'depart_id', 'is_leader', 'disp'];
                            $rows = [];
                            foreach ($addDepartmentIds as $did) {
                                $leader = $isLeaderInDepts[$did] === "true" ? 1 : 0;
                                $order = isset($orderInDepts[$did]) ? $orderInDepts[$did] : '';

                                //更新部门用户关系表
                                if (!$record = DepartmentUser::findOneByWhere(['user_id' => $kaelId, 'depart_id' => $did], '', '', -1)) {
                                    $rows[] = [$kaelId, $did, $leader, $order];
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                } else {
                                    $relateUpdateParams = [];
                                    if ($record['status']) {
                                        $relateUpdateParams['status'] = 0;
                                    }
                                    if ($record['is_leader'] != $leader) {
                                        $relateUpdateParams['is_leader'] = $leader;
//                                        BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                    }
                                    if ($record['disp'] != $order) {
                                        $relateUpdateParams['disp'] = $order;
                                    }
                                    if (!empty($relateUpdateParams)) {
                                        DepartmentUser::updateAll($relateUpdateParams, ['id' => $record['id']]);
                                    }
                                }

                                //更新钉钉部门表 部门领导人
                                if ($leader) {
                                    $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                    if ($dingDepartment['main_leader_id'] != $kaelId) {
//                                        DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                    }
                                }
                            }
                            DepartmentUser::addAllWithColumnRow($cloumns, $rows);
                        }
                        //删除旧关联部门
                        if (!empty($deleteDepartmentIds)) {
                            DepartmentUser::updateAll(['status' => 1], ['user_id' => $kaelId, 'depart_id' => $deleteDepartmentIds]);
//                            DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['main_leader_id' => $kaelId, 'id' => $deleteDepartmentIds]);
                        }
                        $founder = false;
                        //更新员工加入的部门
                        foreach ($departmentIds as $did) {
                            if ($did == 1) $founder = true;
                            if (!in_array($did, $addDepartmentIds) && !in_array($did, $deleteDepartmentIds)) {
                                $params = [];
                                $isLeader = $isLeaderInDepts[$did] === 'true' ? 1 : 0;
                                if ($isLeader != $oldDepartments[$did]['is_leader']) {
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                    $params['is_leader'] = $isLeader;
                                }
                                if (isset($orderInDepts[$did]) && isset($oldDepartments[$did]) && $orderInDepts[$did] != $oldDepartments[$did]['disp']) {
                                    $params['disp'] = $orderInDepts[$did];
                                }
                                if (!empty($params)) {
                                    DepartmentUser::updateAll($params, ['id' => $oldDepartments[$did]['id']]);
                                }
                                $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                if ($isLeader) {
                                    if ($dingDepartment['main_leader_id'] != $kaelId || $dingDepartment['main_leader_name'] != $userInfo['name']) {
//                                        DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                    }
                                } elseif ($dingDepartment['main_leader_id'] == $kaelId) {
                                    echo $kaelId . "\t 不再是部门" . $did . "的负责人";
//                                    DingtalkDepartment::updateAll(['main_leader_id' => 0, 'main_leader_name' => ''], ['main_leader_id' => $kaelId, 'id' => $did]);
                                }
                            }
                        }
                        //更新员工关联kael部门  从主department_id开始向父级依次匹配
                        $mainDingDepartmentToUserInfo = DepartmentUser::findOneByWhere(['is_main' => 1, 'user_id' => $kaelId, 'status' => 0]);
                        $mainDingDepartmentForUser = $mainDingDepartmentToUserInfo['depart_id'] ?? '';
                        if (!$mainDingDepartmentForUser && !empty($departmentIds)) { //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            $params = [];
                            $params['is_main'] = 1;
                            $oldMainDingDepartmentToUserInfo = DepartmentUser::findOneByWhere(['is_main' => 1, 'user_id' => $kaelId, 'status' => 1], '', 'create_time desc');
                            if ($oldMainDingDepartmentToUserInfo) {
                                $params['position_type_id'] = $oldMainDingDepartmentToUserInfo['position_type_id'];
                                $params['job_position_id'] = $oldMainDingDepartmentToUserInfo['job_position_id'];
                                $params['finance_position_id'] = $oldMainDingDepartmentToUserInfo['finance_position_id'];
                            }
                            DepartmentUser::updateAll($params, ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);

                        } elseif ( $mainDingDepartmentForUser && $mainDingDepartmentForUser != $mainDepartId && !in_array($mainDingDepartmentForUser, $departmentIds) && !empty($departmentIds) && in_array($mainDepartId, $departmentIds)) {
                            DepartmentUser::updateAll(['is_main' => 0], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            $params = [
                                'is_main' => 1,
                                'position_type_id' => $mainDingDepartmentToUserInfo['position_type_id'],
                                'job_position_id' => $mainDingDepartmentToUserInfo['job_position_id'],
                                'finance_position_id' => $mainDingDepartmentToUserInfo['finance_position_id']
                            ];
                            DepartmentUser::updateAll($params, ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        }

                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        if ($relateKaelDepartmentId && !$founder) {
                            UserCenter::updateAll(['department_id' => $relateKaelDepartmentId], ['id' => $kaelId]);
                        } elseif (!$founder) {
                            UserCenter::updateAll(['department_id' => 151], ['id' => $kaelId]);
                        }
                    } else {
                        // 新增
                        echo date('Y-m-d H:i:s') . "\t新增员工:\n";
                        echo json_encode($userInfo, true) . "\n";

                        //新增
                        $addParams = [
                            'user_id' => $userInfo['userid'],
                            'name' => $userInfo['name'],
                            'mobile' => $userInfo['mobile'],
                            'avatar' => $userInfo['avatar'],
                            'job_number' => $userInfo['jobnumber'],
                            'union_id' => $userInfo['unionid'],
                            'open_id' => $userInfo['openId'],
                            'departments' => join(',', $userInfo['department']),
                            'department_id' => $userInfo['department'][0],
                            'department_subroot' => $departmentToSubRoot[$userInfo['department'][0]] ?? $userInfo['department'][0],
                            'corp_type' => $corpType,
                        ];
                        if (isset($userInfo['hiredDate']) && !empty($userInfo['hiredDate'])) {
                            $addParams['hired_date'] = date('Y-m-d', $userInfo['hiredDate'] / 1000);
                        }
                        DingtalkUser::add($addParams);
                        if (!empty($userInfo['mobile'])) {
                            if ($user = UserCenter::findOneByWhere(['mobile' => $userInfo['mobile'], 'user_type' => 0], '', -1)) {
                                $kaelId = $user['id'];
                                $params = [];
                                if ($user['user_type']) {
                                    $params['user_type'] = 0;
                                }
                                if ($user['status']) {
                                    $params['status'] = 0;
                                }
                                !empty($params) && UserCenter::updateAll($params, ['id' => $user['id']]);
                                //更新钉钉员工关联kael编号
                                DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);
                                echo "[手机号]钉钉账号:" . $userInfo['userid'] . "\t->绑定->\tkael账号:" . $user['id'] . "\n";
                            }
                        }

                        if (!$user) {
                            //新增kael
                            $params = [
                                'username' => $userInfo['name'],
                                'password' => md5('1!Aaaaaaa'),
                                'sex' => 1,
                                'work_number' => $userInfo['jobnumber'],
                                'mobile' => isset($userInfo['mobile']) ? $userInfo['mobile'] : '',
                                'user_type' => 0
                            ];

                            $kaelId = UserCenter::addUser($params);
                            echo "新增kael账号:\t" . $kaelId . "\n";
                        }
                        //更新钉钉员工关联kael编号
                        DingtalkUser::updateAll(['kael_id' => $kaelId], ['user_id' => $userInfo['userid']]);

                        $founder = false;
                        //更新实际部门相关
                        $departmentIds = !is_array($userInfo['department']) ? json_decode($userInfo['department'], true) : $userInfo['department'];
                        $isLeaderInDepts = self::convertJsonMapToArray($userInfo['isLeaderInDepts']);
                        $orderInDepts = self::convertJsonMapToArray($userInfo['orderInDepts']);
                        $cloumns = ['user_id', 'depart_id', 'is_leader', 'disp'];
                        $rows = [];
                        foreach ($departmentIds as $did) {
                            if ($did == 1) $founder = true;
                            $leader = $isLeaderInDepts[$did] === "true" ? 1 : 0;
                            $order = isset($orderInDepts[$did]) ? $orderInDepts[$did] : '';

                            //更新实际部门关系
                            if (!$record = DepartmentUser::findOneByWhere(['user_id' => $kaelId, 'depart_id' => $did], '', '', -1)) {
                                $rows[] = [$kaelId, $did, $leader, $order];
//                                BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                            } else {
                                $relateUpdateParams = [];
                                if ($record['status']) {
                                    $relateUpdateParams['status'] = 0;
                                }
                                if ($record['is_leader'] != $leader) {
                                    $relateUpdateParams['is_leader'] = $leader;
//                                    BusinessDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['depart_id' => $did]);
                                }
                                if ($record['disp'] != $order) {
                                    $relateUpdateParams['disp'] = $order;
                                }
                                if (!empty($relateUpdateParams)) {
                                    DepartmentUser::updateAll($relateUpdateParams, ['id' => $record['id']]);
                                }
                            }

                            //更新钉钉部门表 部门领导人
                            if ($leader) {
                                $dingDepartment = DingtalkDepartment::findOneByWhere(['id' => $did]);
                                if ($dingDepartment['main_leader_id'] != $kaelId) {
//                                    DingtalkDepartment::updateAll(['main_leader_id' => $kaelId, 'main_leader_name' => $userInfo['name']], ['id' => $did]);
                                }
                            }
                        }
                        DepartmentUser::addAllWithColumnRow($cloumns, $rows);

                        //更新员工关联kael部门
                        $mainDingDepartmentForUser = DepartmentUser::find()->select(['depart_id'])->where(['is_main' => 1, 'user_id' => $kaelId, 'status' => 0])->scalar();
                        if (!$mainDingDepartmentForUser && !empty($departmentIds)) { //如果没有并且钉钉部门不为空 则默认设置第一个钉钉部门为主部门
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            DepartmentUser::updateAll(['is_main' => 1], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        } elseif ($mainDingDepartmentForUser && !in_array($mainDingDepartmentForUser, $departmentIds) && !empty($departmentIds)) {
                            DepartmentUser::updateAll(['is_main' => 0], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            $mainDingDepartmentForUser = $mainDepartId ?? $departmentIds[0];
                            DepartmentUser::updateAll(['is_main' => 1], ['user_id' => $kaelId, 'depart_id' => $mainDingDepartmentForUser]);
                            DingtalkUser::updateAll(['department_id' => $mainDingDepartmentForUser], ['user_id' => $userInfo['userid']]);
                        }
                        $relateKaelDepartmentId = self::getRelateKaelDepartment($mainDingDepartmentForUser);
                        if ($relateKaelDepartmentId && !$founder) {
                            UserCenter::updateAll(['department_id' => $relateKaelDepartmentId], ['id' => $kaelId]);
                        } elseif (!$founder) {
                            UserCenter::updateAll(['department_id' => 151], ['id' => $kaelId]);
                        }
                    }
                }
            }
        }

        //new
        $deleteUserIds = array_diff($allUserIds,$newAllUserIds);
        if(empty($deleteUserIds)){
            return ;
        }
        $deleteKaelInfos = DingtalkUser::findList(['user_id'=>$deleteUserIds],'user_id','user_id,kael_id');
        $deleteUids = array_column($deleteKaelInfos,'kael_id');
        echo date('Y-m-d H:i:s')."\t需要删除员工如下:\n";
        echo json_encode($deleteUids)."\n";
        if(!empty($deleteUserIds)){
            foreach ($deleteUserIds as $userId){
                try{
                    $dingInfo = DingTalkApiJZ::getUserInfo($userId);
                    continue;
                }catch (\Exception $e){
                    if($e->getMessage() != '[DING]找不到该用户'){
                        echo date('Y-m-d H:i:s')."\t[delete:no] user_id:".$userId."\t".$e->getMessage()."\n";
                        continue;
                    }
                }
                echo date('Y-m-d H:i:s')."\t[delete:yes] user_id:".$userId."\t".$e->getMessage()."\n";

                $kaelId = isset($deleteKaelInfos[$userId])?$deleteKaelInfos[$userId]['kael_id']:0;
                if(!$kaelId){
                    echo date('Y-m-d H:i:s')."\t[error:kael_id=0] user_id:".$userId."\t".$e->getMessage()."\n";
                    continue;
                }
                if(!UserCenter::findOneByWhere(['id'=>$kaelId],'',-1)){
                    echo date('Y-m-d H:i:s')."\t[error:kael account not find] user_id:".$userId."\t".$e->getMessage()."\n";
                    continue;
                }
                $transKael = DingtalkUser::getDb()->beginTransaction();
                $tranEhr = BusinessLineRelateStaff::getDb()->beginTransaction();
                try {
                    //钉钉表
                    DingtalkUser::updateAll(['status'=>1],['user_id'=>$userId]);
                    //用户表
                    UserCenter::updateAll(['status'=>1],['id'=>$kaelId]);
                    //部门关联表
                    DepartmentUser::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    //用户附属信息表
                    UserInfo::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    //钉钉部门主表
//                    DingtalkDepartment::updateAll(['main_leader_id'=>0,'main_leader_name'=>''],['main_leader_id'=>$kaelId]);
//                    BusinessDepartment::updateAll(['main_leader_id'=>0,'main_leader_name'=>''],['main_leader_id'=>$kaelId]);
                    //ehr表
                    AuthUser::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    AuthUserRoleDataPermRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    AuthUserRoleRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    BusinessLineRelateSecondLeader::updateAll(['status'=>1],['leader_id'=>$kaelId]);
                    BusinessLineRelateStaff::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    ConcernAnniversaryRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    ConcernBirthdayRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    PsAnswer::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PsAnswer::updateAll(['status'=>1],['be_evaluator_id'=>$kaelId]);
                    PsEvaluateRelate::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PsEvaluateRelate::updateAll(['status'=>1],['be_evaluator_id'=>$kaelId]);
                    PsMessageDetail::updateAll(['status'=>1],['evaluator_id'=>$kaelId]);
                    PushCenterAcceptUserRecord::updateAll(['status'=>1],['user_id'=>$kaelId]);
                    PushCenterLog::updateAll(['status'=>1],['accept_user_id'=>$kaelId]);
                    StaffFieldEditRecord::updateAll(['status'=>1],['staff_id'=>$kaelId]);
                    $transKael->commit();
                    $tranEhr->commit();
                } catch (\Exception $e){
                    $transKael->rollBack();
                    $tranEhr->rollBack();
                    throw $e;
                }
            }
        }
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
            if(! $dingUserList = DingtalkUser::findListByWhereWithWhereArr(['birthday'=>'','corp_type'=>1],[['>','auto_id',$id]],'auto_id,user_id,name,birthday','auto_id asc',10)){
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
            sleep(60);
        }
        echo date('Y-m-d H:i:s')."\t*********************更新结束\n";
    }


    public function actionUpdate2(){
        $dingUsers = DingtalkUser::findList(['status'=>1]);
        foreach ($dingUsers as $dingUser){
            if(!$dingUser['kael_id']){
                if(!empty($dingUser['mobile'])){
                    if($user = UserCenter::findOneByWhere(['mobile'=>$dingUser['mobile']],'',-1)){
                        $params = [];
                        if($user['user_type']){
                            $params['user_type'] = 0;
                        }
                        if($user['status'] && DingtalkUser::find()->select('user_id')->where(['mobile'=>$dingUser['mobile'],'status'=>0])->scalar()){
                            $params['status'] = 0;
                        }
                        !empty($params) &&  UserCenter::updateAll($params,['id'=>$user['id']]);
                        $kaelId = $user['id'];
                        //更新钉钉员工关联kael编号
                        DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$dingUser['user_id']]);
                        echo "[手机号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$user['id']."\n";
                    }
                }
//                if(!$user && !empty($dingUser['job_number'])){
//                    if($user = UserCenter::findOneByWhere(['work_number'=>$dingUser['job_number']],'',-1)){
//                        if($user['user_type']){
//                            UserCenter::updateAll(['user_type'=>0],['id'=>$user['id']]);
//                        }
//                        if(!$user['status']){
//                            UserCenter::updateAll(['status'=>1],['id'=>$user['id']]);
//                        }
//                        $kaelId = $user['id'];
//                        //更新钉钉员工关联kael编号
//                        DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$dingUser['user_id']]);
//                        echo "[工号]钉钉账号:".$dingUser['user_id']."\t->绑定->\tkael账号:".$user['id']."\n";
//                    }
//                }sx
                if(!$user){
                    //新增kael
                    $params = [
                        'username'=>$dingUser['name'],
                        'password'=>md5('1!Aaaaaaa'),
                        'sex'=>1,
                        'work_number'=>$dingUser['job_number'],
                        'mobile'=>isset($dingUser['mobile'])?$dingUser['mobile']:'',
    //                                'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                        'user_type'=>0,
                        'status'=>1
                    ];
                    $kaelId = UserCenter::addUser($params);
                    DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$dingUser['user_id']]);
                    echo "新增kael账号:\t".$kaelId."\n";
                }
            }else{
                $user = UserCenter::findOneByWhere(['id'=>$dingUser['kael_id']],'',-1);
                if($user && $user['mobile'] == $dingUser['mobile']){
                    if($dingVoildUser = DingtalkUser::findOneByWhere(['kael_id'=>$dingUser['kael_id']])){
                        $dingVoildUser['job_number'] != $user['work_number'] &&
                        UserCenter::updateAll(['work_number'=>$dingVoildUser['job_number']],['id'=>$dingUser['kael_id']]);
                    }elseif($user['work_number'] != $dingUser['job_number']){
                        UserCenter::updateAll(['work_number'=>$dingUser['job_number']],['id'=>$dingUser['kael_id']]);
                    }

                    if(!$user['status'] && !DingtalkUser::find()->select('user_id')->where(['mobile'=>$dingUser['mobile'],'status'=>0])->scalar()){
                        UserCenter::updateAll(['status'=>1],['id'=>$dingUser['kael_id']]);
                    }
                }else{
                    //新增kael
                    $params = [
                        'username'=>$dingUser['name'],
                        'password'=>md5('1!Aaaaaaa'),
                        'sex'=>1,
                        'work_number'=>$dingUser['job_number'],
                        'mobile'=>isset($dingUser['mobile'])?$dingUser['mobile']:'',
                        //                                'email'=>isset($userInfo['email'])?$userInfo['email']:'',
                        'user_type'=>0,
                        'status'=>1
                    ];
                    $kaelId = UserCenter::addUser($params);
                    DingtalkUser::updateAll(['kael_id'=>$kaelId],['user_id'=>$dingUser['user_id']]);
                    echo "新增kael账号:\t".$kaelId."\n";
                }
            }
        }
    }

    public static function tmpIdToWorknumber($id){
        $workNunmber = '';
        $id = intval($id);
        while($id){
            $next = $id%9 + 1;
            $id = intval($id/9);
            $workNunmber = $next.$workNunmber;
        }
        $workNunmber = 1000000 + intval($workNunmber);
        $workNunmber = 'JZ'.$workNunmber;
        return $workNunmber;
    }
}
