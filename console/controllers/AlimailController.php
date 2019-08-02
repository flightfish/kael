<?php
namespace console\controllers;

use common\libs\AliMailApi;
//use common\libs\DingTalkApi;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use Yii;
use yii\console\Controller;


class AlimailController extends Controller
{

    //同步邮箱
    public function actionSynEmailAccount(){
//        echo json_encode(AliMailApi::departmentList(),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
//        echo "\n";
//        echo json_encode(AliMailApi::userInfoList(['wangchao@knowbox.cn']),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        //所有员工
        $allDingUserList = DingtalkUser::findList([],'','auto_id,department_subroot,email,name');
        $allSubrootIds = array_filter(array_unique(array_column($allDingUserList,'department_subroot')));
        $allSubrootList = DingtalkDepartment::findList(['id'=>$allSubrootIds],'','id,name');
        $departmentIdToAlimail = array_column(AliMailApi::departmentList(),'departmentId','customDepartmentId');
        foreach ($allSubrootList as $v){
            if(isset($departmentIdToAlimail[$v['id']])){
                continue;
            }
            $alimailDeparmentInfo = AliMailApi::createDepartment($v['id'],$v['name']);
            $departmentIdToAlimail[$v['id']] = $alimailDeparmentInfo['departmentId'];
        }
        echo json_encode($departmentIdToAlimail)."\n";
        $allEmails = array_filter(array_unique(array_column($allDingUserList,'email')));
        $emailToDepartment = array_column(AliMailApi::userInfoList($allEmails),'departmentId','email');
        echo json_encode($emailToDepartment,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
        foreach ($allDingUserList as $v){
            $aliDepartmentId = $departmentIdToAlimail[$v['department_subroot']] ?? Yii::$app->params['alimail_departmentRoot'];
            if(!isset($emailToDepartment[$v['email']])){
                AliMailApi::createUser($v['name'],$v['email'],$aliDepartmentId);
            }elseif($aliDepartmentId != $emailToDepartment[$v['email']]){
                AliMailApi::updateUserDepartment($aliDepartmentId,$v['email']);
            }
        }
        exit();

        if(\Yii::$app->params['env'] != 'prod'){
            return false;
        }
        if(exec('ps -ef|grep "email/syn-email-account"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo date('Y-m-d H:i:s')."\tis_running\n";
            exit();
        }
        echo date('Y-m-d H:i:s')."\t开始运行:\n";
        //删除
        $listForDel = DingtalkUser::find()
            ->select('user_id,email')
            ->where(['email_created_ali'=>[1,3],'status'=>1]) //已创建 注销中
            ->andWhere(['like','email','@knowbox.cn'])
            ->asArray(true)
            ->all();
        if(!empty($listForDel)){
            $emailForDelAll = array_column($listForDel,'email');
            $emailToId = array_column($listForDel,'user_id','email');
            $emailForDelChunk = array_chunk($emailForDelAll,10);
            foreach ($emailForDelChunk as $emailForDel){
                $checkList = EmailApi::batchCheck($emailForDel);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 1){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    DingtalkUser::updateAll(['email_created_ali'=>4],['user_id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //查询还有没有其他账号在用
                            $others = DingtalkUser::find()->where(['status'=>0,'email'=>$v['user']])
                                ->asArray(true)->limit(1)->one();
                            if(empty($others)){
                                //没有有效账号则删除
                                echo 'del - '.$v['user']."\n";
                                echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                                try{
                                    EmailApi::deleteUser($v['user']);
                                }catch (\Exception $e){
                                    echo date('Y-m-d H:i:s').$e->getMessage()."\n";
                                    continue;
                                }
                            }
                            DingtalkUser::updateAll(['email_created_ali'=>4],['user_id'=>$emailToId[$v['user']]]);
                        }
                    }
                }
            }
        }

        //创建
        $listUpdate = DingtalkUser::find()
            ->select('user_id,name,email')
            ->where(['email_created_ali'=>[0,4],'status'=>0]) //创建中 已注销
            ->andWhere(['like','email','@knowbox.cn'])
            ->asArray(true)
            ->all();
        if(!empty($listUpdate)){
            $emailForUpdateAll = array_column($listUpdate,'email');
            $emailToName = array_column($listUpdate,'name','email');
            $emailToId = array_column($listUpdate,'user_id','email');
            $emailForUpdateChunk = array_chunk($emailForUpdateAll,10);
            foreach ($emailForUpdateChunk as $emailForUpdate){
                $checkList = EmailApi::batchCheck($emailForUpdate);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
                        echo $v['user']."\t检查结果:type=".$v['type']."\n";
                        if($v['type'] == -1){
                            continue;
                        }
                        if($v['type'] == 0){
                            if(Yii::$app->params['env'] != 'prod'){
                                if(strpos($v['user'],'emailtest') === false){
                                    try{
                                        $emailToId[$v['user']];
                                    }catch (\Exception $e){
                                        var_dump($v);
                                        throw $e;
                                    }
                                    DingtalkUser::updateAll(['email_created_ali'=>1],['user_id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //添加
                            echo 'add - '. $v['user']."\n";
                            echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                            try{
                                EmailApi::addUser($v['user'],$emailToName[$v['user']],'1Knowbox!');
                            }catch (\Exception $e){
                                echo date('Y-m-d H:i:s')."\t创建邮箱失败\t".$v['user']."\t".$e->getMessage()."\n";
                                continue;
                            }
                            DingTalkApi::sendWorkMessage('text',['content'=>"欢迎亲爱的盒子:\n\t公司邮箱已经为您开通啦,请尽快登陆并修改密码\n\t登陆地址:https://exmail.qq.com\n\t账号:{$v['user']}\n\t密码:1Knowbox!"],$emailToId[$v['user']]);
                        }
                        DingtalkUser::updateAll(['email_created_ali'=>1],['user_id'=>$emailToId[$v['user']]]);
                    }
                }
            }
        }


        //删除
        //查询成员
        $allUsers = EmailApi::getDepartmentListUser();
        $allUsers = $allUsers['userlist'];
        $allUsers = array_column($allUsers,'userid');
        //所有有效的
        $allUserEmail = DingtalkUser::find()
            ->select('email')
            ->where(['status'=>0])
            ->andWhere(['like','email','knowbox.cn'])
            ->asArray()->column();
        $allUserEmail = array_map('trim',$allUserEmail);
        //无效成员
        $inValidList = array_diff($allUsers,$allUserEmail);
        foreach ($inValidList as $v){
            echo "invalid email:".$v."\n";
            try{
                EmailApi::deleteUser($v);
            }catch (\Exception $e){
                echo date('Y-m-d H:i:s').$e->getMessage()."\n";
            }
        }
    }

}
