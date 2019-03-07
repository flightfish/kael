<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
use usercenter\modules\meican\models\MeicanApi;
use Yii;
use yii\console\Controller;


class MeicanController extends Controller
{
    /**
     * 初始化用户信息到Meican
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "meican/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        if(empty(Yii::$app->params['meican_corp_prefix'])){
            echo "未设置美餐信息\n";
            exit();
        }
        try {
            $allMembers = MeicanApi::listMember();
            $allMemberUserIds = [];
            foreach ($allMembers as $v){
                if(empty($v['removed'])){
                    $allMemberUserIds[] = intval($v['email']);
                }
            }
            //全部有效的
            if(!empty(Yii::$app->params['env']) && Yii::$app->params['env'] == 'dev'){
                $allValidUserIds = CommonUser::find()->select('id')->where(['status'=>0,'user_type'=>0,'mobile'=>'13683602952'])->asArray(true)->column();
            }else{
                $allValidUserIds = CommonUser::find()->select('id')->where(['status'=>0,'user_type'=>0])->asArray(true)->column();
            }
            $allValidUserIds = array_map('intval',$allValidUserIds);
            //删除旧的
            $delUserIds = array_diff($allMemberUserIds,$allValidUserIds);
            $addUserIds = array_diff($allValidUserIds,$allMemberUserIds);
            foreach ($addUserIds as $v){
                MeicanApi::addMember($v);
            }
            foreach ($delUserIds as $v){
                MeicanApi::delMember($v);
            }
        }catch (\Exception $e){
            throw $e;
        }

    }
}
