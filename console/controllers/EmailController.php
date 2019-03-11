<?php
namespace console\controllers;

use common\libs\EmailApi;
use common\models\CommonUser;
use Yii;
use yii\console\Controller;


class EmailController extends Controller
{
    /**
     * 初始化用户信息到EMAIL
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "email/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $listForDel = CommonUser::getDb()->createCommand("select id,username,email from `user` where user_type = 0 and email_created = 1 and email != '' and status!=0")->queryAll();
        $listUpdate = CommonUser::getDb()->createCommand("select id,username,email from `user` where user_type = 0 and email_created = 0 and email != '' and status=0")->queryAll();


        /**
        {
        "errcode": 0,
        "errmsg": "ok",
        "list": [
        {"user":"zhangsan@bjdev.com", "type":1}, 帐号类型。-1:帐号号无效; 0:帐号名未被占用; 1:主帐号; 2:别名帐号; 3:邮件群组帐号
        {"user":"zhangsangroup@shdev.com", "type":3}
        ]
        }
         */
        if(!empty($listForDel)){
            $emailForDelAll = array_column($listForDel,'email');
            $emailToId = array_column($listForDel,'id','email');
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
                                    CommonUser::updateAll(['email_created'=>0],['id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //查询还有没有其他账号在用
                            $others = CommonUser::find()->where(['status'=>0,'email'=>$v['user'],'user_type'=>0])
                                ->asArray(true)->limit(1)->one();
                            if(empty($others)){
                                //没有有效账号则删除
                                echo 'del - '.$v['user']."\n";
                                echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                                EmailApi::deleteUser($v['user']);
                            }
                        }
                        CommonUser::updateAll(['email_created'=>0],['id'=>$emailToId[$v['user']]]);
                    }
                }
            }

        }
        if(!empty($listUpdate)){
            $emailForUpdateAll = array_column($listUpdate,'email');
            $emailToName = array_column($listUpdate,'username','email');
            $emailToId = array_column($listUpdate,'id','email');
            $emailForUpdateChunk = array_chunk($emailForUpdateAll,10);
            foreach ($emailForUpdateChunk as $emailForUpdate){
                $checkList = EmailApi::batchCheck($emailForUpdate);
                if(!empty($checkList['list'])){
                    foreach ($checkList['list'] as $v){
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
                                    CommonUser::updateAll(['email_created'=>1],['id'=>$emailToId[$v['user']]]);
                                    continue;
                                }
                            }
                            //添加
                            echo 'add - '. $v['user']."\n";
                            echo json_encode($v,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)."\n";
                            EmailApi::addUser($v['user'],$emailToName[$v['user']],'Know11');
                        }
                        CommonUser::updateAll(['email_created'=>1],['id'=>$emailToId[$v['user']]]);
                    }
                }
            }

        }

        //查询成员
        $allUsers = EmailApi::getDepartmentListUser();
        $allUsers = $allUsers['userlist'];
        echo json_encode($allUsers,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";
    }
}
