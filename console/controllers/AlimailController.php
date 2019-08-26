<?php
namespace console\controllers;

use common\libs\AliMailApi;
use common\libs\DingDing;
use common\libs\DingTalkApi;
use common\models\AlimailStatus;
use common\models\DBCommon;
use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use Yii;
use yii\console\Controller;


class AlimailController extends Controller
{

    public function actionListOther(){
        //非员工邮箱
        $allEmails = array_column(DingtalkUser::findList([],'','email'),'email');
        $aliMails = [];
        $aliMailStart = 0;
        while(1){
            $ret = AliMailApi::allUserList($aliMailStart,200);
            echo json_encode(['start'=>$aliMailStart,'ret'=>$ret],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
            $aliMailStart += 200;
            if(empty($ret['accounts'])){
                break;
            }
            $aliMails = array_merge($aliMails,$ret['accounts']);
        }
        $aliMails = array_unique(array_column($aliMails,'email'));
        $diff = array_diff($allEmails,$aliMails);
        echo json_encode(['diff'=>$diff],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
    }

    //alimail/update-pass
    public function actionUpdatePass(){
        if(\Yii::$app->params['env'] != 'prod'){
            return false;
        }
        $allUserList = DingtalkUser::findList([],'','email,user_id');
        foreach ($allUserList as $v){
            if(!empty($v['email'])){
                $passwd = $this->genPasswd(rand(1,3),rand(6,8),rand(1,3),0);
                echo "{$v['email']} {$passwd} \n";
                try{
                    AliMailApi::updateUserPasswd($v['email'],$passwd);
                    $param = [
                        "title"=> "阿里邮箱密码修改通知",
                        "markdown"=>"您的阿里邮箱密码已重置，请尽快登陆并修改密码  \n 登陆地址:https://qiye.aliyun.com  \n 账号:{$v['email']}  \n 密码:{$passwd}  \n 重置时间:".date("Y-m-d H:i:s"),
                        "btn_orientation"=> "1",
                        "btn_json_list"=> [
                            [
                                "title"=> "点击查看",
                                "action_url"=> 'https://qiye.aliyun.com'
                            ]
                        ]
                    ];
                    DingTalkApi::sendWorkMessage('action_card', $param, $v['user_id']);
                }catch (\Exception $e){
                    echo $e->getMessage()."\n";
                }

            }
        }
    }

    public function actionUpdatePassTest(){
        $allUserList = DingtalkUser::findList(['kael_id'=>11090],'','email,user_id');
        foreach ($allUserList as $v){
            if(!empty($v['email'])){
                $passwd = $this->genPasswd(rand(1,3),rand(6,8),rand(1,3),0);
                echo "{$v['email']} {$passwd} \n";
                AliMailApi::updateUserPasswd($v['email'],$passwd);
                $param = [
                    "title"=> "阿里邮箱密码修改通知",
                    "markdown"=>"您的阿里邮箱密码已重置，请尽快登陆并修改密码  \n 登陆地址:https://qiye.aliyun.com  \n 账号:{$v['email']}  \n 密码:{$passwd}  \n 重置时间:".date("Y-m-d H:i:s"),
                    "btn_orientation"=> "1",
                    "btn_json_list"=> [
                        [
                            "title"=> "点击查看",
                            "action_url"=> 'https://qiye.aliyun.com'
                        ]
                    ]
                ];
                DingTalkApi::sendWorkMessage('action_card', $param, $v['user_id']);
                break;
            }
        }
    }

    //生成密码
    private function genPasswd($c1,$c2,$c3,$c4){
        //大小写数字特殊字符1Knowbox! 8-64位  3种
        $part1 = [1,2,3,4,5,6,7,8,9,0];
        $part2 = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $part3 = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $part4 = ['!','@','#','$','%','&','*','(',')'];
        $ret = [1=>'',2=>'',3=>'',4=>''];
        for($i=0;$i<$c1;$i++){
            shuffle($part1);
            $ret[1] .= $part1[0];
        }
        for($i=0;$i<$c2;$i++){
            shuffle($part2);
            $ret[2] .= $part2[0];
        }
        for($i=0;$i<$c3;$i++){
            shuffle($part3);
            $ret[3] .= $part3[0];
        }
        for($i=0;$i<$c4;$i++){
            shuffle($part4);
            $ret[4] .= $part4[0];
        }
        shuffle($ret);
        return join('',$ret);
    }


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
                    $passwd = $this->genPasswd(rand(1,3),rand(6,8),rand(1,3),rand(0,2));
                    DingTalkApi::sendWorkMessage('text',
                        ['content'=>"欢迎亲爱的盒子:\n\t公司邮箱已经为您开通啦,请尽快登陆并修改密码\n\t登陆地址:https://qiye.aliyun.com\n\t账号:{$successEmail['email']}\n\t密码:{$passwd}"],
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
