<?php
namespace console\controllers;

use common\libs\AliMailApi;
use common\libs\DingTalkApi;
use common\models\AlimailStatus;
use common\models\DBCommon;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use Yii;
use yii\console\Controller;


class AlimailController extends Controller
{

    //同步删除邮箱
    public function actionSynDel(){
        $allDingUserList = DingtalkUser::findList([],'','email');
        $allDingEmails = array_filter(array_unique(array_column($allDingUserList,'email')));
        $historyMails = array_column(AlimailStatus::findList([],'','email'),'email');
        $delMails = array_diff($historyMails,$allDingEmails);
        if(empty($delMails)){
            exit();
        }
        $aliDelMails = array_unique(array_column(AliMailApi::userInfoList($delMails),'email','email'));
        foreach ($delMails as $v){
            if(!empty($aliDelMails[$v])){
                echo "del {$v} \n";
                AliMailApi::userDel($v);
            }
            AlimailStatus::updateAll(['status'=>AlimailStatus::STATUS_INVALID],['email'=>$v]);
            DingtalkUser::updateAll(['email_created_ali'=>4],['email'=>$v,'status'=>DingtalkUser::STATUS_INVALID]);
        }
    }


    //同步邮箱
    public function actionSynEmailAccount(){
        //删除测试
        AliMailApi::userDel('wangchao@knowbox.cn');
        //所有员工
        $allDingUserList = DingtalkUser::findList([],'','auto_id,department_subroot,email,name,user_id');
        $emailToId = array_column($allDingUserList,'user_id','email');

        $allSubrootIds = array_filter(array_unique(array_column($allDingUserList,'department_subroot')));
        $allSubrootList = DingtalkDepartment::findList(['id'=>$allSubrootIds],'','id,name');
        $allSubrootList[] = ['id'=>'1','name'=>'创始人'];
        $departmentIdToAlimail = array_column(AliMailApi::departmentList(),'departmentId','customDepartmentId');
        foreach ($allSubrootList as $v){
            if(isset($departmentIdToAlimail[$v['id']])){
                continue;
            }
            $alimailDeparmentInfo = AliMailApi::createDepartment($v['id'],$v['name']);
            $departmentIdToAlimail[$v['id']] = $alimailDeparmentInfo['departmentId'];
        }
        $allEmails = array_filter(array_unique(array_column($allDingUserList,'email')));
        $emailToDepartment = array_column(AliMailApi::userInfoList($allEmails),'departmentId','email');
        $accountForCreate = [];
        $accountForUpdateDept = [];
        foreach ($allDingUserList as $v){
            if(empty($v['email'])){
                continue;
            }
            $aliDepartmentId = $departmentIdToAlimail[$v['department_subroot']] ?? $departmentIdToAlimail[1];
            if(!isset($emailToDepartment[$v['email']])){
                $accountForCreate[] = [
                    "name"=>$v['name'],
                    "passwd"=>'1Knowbox!',
                    "email"=>$v['email'],
                    "departmentId"=>$aliDepartmentId
                ];
            }elseif($aliDepartmentId != $emailToDepartment[$v['email']]){
                $accountForUpdateDept[$aliDepartmentId][] = $v['email'];
            }
        }
        $accountForCreateChunk = array_chunk($accountForCreate,100);
        foreach ($accountForCreateChunk as $v){
            $alimailStatusRows = array_map(function($v){return [$v['email'],0];},$v);
            DBCommon::batchInsertAll(AlimailStatus::tableName(),['email','status'],$alimailStatusRows,AlimailStatus::getDb(),"UPDATE");
            $retData = AliMailApi::createUserBatch($v);
            foreach ($retData['success']??[] as $successEmail){
                DingtalkUser::updateAll(['email_created_ali'=>1],['user_id'=>$successEmail['email']]);
                echo 'create '. $successEmail['email']."\n";
                if(\Yii::$app->params['env'] === 'prod' || $successEmail['email']=='wangchao@knowbox.cn'){
                    DingTalkApi::sendWorkMessage('text',
                        ['content'=>"欢迎亲爱的盒子:\n\t公司邮箱已经为您开通啦,请尽快登陆并修改密码\n\t登陆地址:https://qiye.aliyun.com\n\t账号:{$successEmail['email']}\n\t密码:1Knowbox!"],
                        $emailToId[$successEmail['email']]);
                }
            }
        }
        foreach ($accountForUpdateDept as $aliDepartmentId=>$emails){
            $emailChunk = array_chunk($emails,100);
            foreach ($emailChunk as $v){
                echo "update mail deparment ".join(',',$v)."\n";
                AliMailApi::updateUserDepartment($aliDepartmentId,$v);
            }
        }
    }

}
