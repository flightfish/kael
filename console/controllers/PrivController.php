<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\libs\BossApi;
use common\libs\UserToken;
use common\models\CommonUser;
use common\models\Department;
use common\models\DingtalkDepartment;
use common\models\DingtalkDepartmentUser;
use common\models\DingtalkUser;
use common\models\live\LiveEmployee;
use common\models\RelateUserPlatform;
use usercenter\modules\meican\models\MeicanApi;
use Yii;
use yii\console\Controller;


class PrivController extends Controller
{
    public function actionBdp(){
        //bdp 6000
        $sql = <<<SQL
insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,6000
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=6000
where a.`status` = 0 and a.user_type=0 and b.relate_id is null
SQL;
        RelateUserPlatform::getDb()->createCommand($sql)->execute();

        //okr
        $sql = <<<SQL
insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,9001
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=9001
where a.`status` = 0 and a.user_type=0 and b.relate_id is null
SQL;
        RelateUserPlatform::getDb()->createCommand($sql)->execute();

        //wiki
        $sql = <<<SQL
insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,90003
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=90003
where a.`status` = 0 and a.user_type=0 and b.relate_id is null
SQL;
        RelateUserPlatform::getDb()->createCommand($sql)->execute();


        /**
        boss 50004
        io 50005
        teacher not 50006
         */
        /*
        //boss
        $sql = <<<SQL
insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,50004
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=50004
where a.`status` = 0 and a.user_type=0 and b.relate_id is null and a.department_id=158
SQL;
        RelateUserPlatform::getDb()->createCommand($sql)->execute();
        //io
        $sql = <<<SQL
insert into relate_user_platform (user_id,platform_id)
select DISTINCT a.id,50005
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=50005
where a.`status` = 0 and a.user_type=0 and b.relate_id is null and a.department_id=158
SQL;
        */
        RelateUserPlatform::getDb()->createCommand($sql)->execute();

    }



    public function actionCurlBoss(){
        if(exec('ps -ef|grep "priv/curl-boss"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
//        $userList = CommonUser::find()->where(['department_id'=>158,'status'=>0])->asArray(true)->all();
        $sql = <<<SQL
select DISTINCT a.*
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=50004
where a.`status` = 0 and a.user_type=0 and b.relate_id > 0 and a.department_id in (158,155)
SQL;
        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        foreach ($userList as $user){
            $user['login_ip'] = '127.0.0.1';
            $token = UserToken::userToToken($user);

            $headers = [
                "Cookie:UCENTER_IUCTOKEN={$token}",
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*"
            ];
            echo $user['id']."\n";
            $ret = AppFunc::curlPost(Yii::$app->params['boss_url'].'/permissionsMenu.do',[],$headers);
            echo $ret."\n";
            sleep(1);
        }
    }


    public function actionBossAllNew(){
        if(exec('ps -ef|grep "priv/boss-all-new"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $bossId = Yii::$app->params['boss_id'];
        $sql = <<<SQL
select DISTINCT a.*
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id={$bossId} and b.status=0
where a.`status` = 0 and a.user_type=0 and b.relate_id > 0
SQL;
        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        $kaelIdToBaseName = array_column(DingtalkUser::findList([],'','kael_id,base_name'),'base_name','kael_id');

        foreach ($userList as $user){
            $user['name'] = $user['username'];
            $user['user_id'] = $user['id'];
            $user['base_name'] = $kaelIdToBaseName[$user['id']] ?? '';

            $deptList = [];
            $dingtalkUser = DingtalkUser::findOneByWhere(['kael_id'=>$user['id']]);
            if(!empty($dingtalkUser)){
                $mainDeptId = $dingtalkUser['department_id'];
                if($dingtalkUser['corp_type'] == 2 && $mainDeptId == 1){
                    $mainDeptId = 2;
                }
                $dingtalkDepaertmentUser = DingtalkDepartmentUser::findList(['kael_id'=>$user['id']]);
                $deptIds = array_values(array_unique(array_column($dingtalkDepaertmentUser,'department_id')));
                !in_array($mainDeptId,$deptIds) && $deptIds[] = $mainDeptId;
                $deptListAll = DingtalkDepartment::findList(['id'=>$deptIds],'','id,name,path_name_ding,path_id');
                foreach ($deptListAll as $v){
                    $deptList[] = [
                        'id'=>intval($v['id']),
                        'name'=>$v['name'],
                        'path_name'=>$v['path_name_ding'],
                        'path_id'=>$v['path_id'],
                        'is_main'=>$v['id'] == $mainDeptId ? 1 : 0,
                    ];
                }
                if($mainDeptId == 1){
                    $deptList[] = [
                        'id'=>1,
                        'name'=>'小盒科技',
                        'path_name'=>'小盒科技',
                        'path_id'=>'|1|',
                        'is_main'=>1,
                    ];
                }elseif($mainDeptId == 2){
                    $deptList[] = [
                        'id'=>2,
                        'name'=>'兼职辅导',
                        'path_name'=>'兼职辅导',
                        'path_id'=>'|2|',
                        'is_main'=>1,
                    ];
                }
            }


            $user['dept_list'] = $deptList;



            $headers = [
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*",
                "Content-Type: application/json",
            ];
            echo json_encode($user,64|256)."\n";
            $user = json_encode($user,64|256);
            $ret = AppFunc::curlPost(Yii::$app->params['boss_url'].'/employee/employeeValidateForKael.do',$user,$headers);
//            $ret = AppFunc::curlPost('https://beta-bslive.knowbox.cn/employee/employeeValidateForKael.do',$user,$headers);
            echo $ret."\n";
            //sleep(1);
        }
    }

    public function actionBossUpdateNew(){
        if(exec('ps -ef|grep "priv/boss-update-new"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $userList = CommonUser::find()->where(['status'=>0,'department_id'=>[158,155]])->asArray(true)->all();
        $privList = RelateUserPlatform::find()->select('user_id')->where(['status'=>0,'platform_id'=>50004])->asArray(true)->all();
        $privList = array_column($privList,'user_id','user_id');
//        $sql = <<<SQL
//select DISTINCT a.*
//from `user` a
// left join relate_user_platform b on a.id=b.user_id and b.platform_id=50004 and b.status=0
//where a.`status` = 0 and a.user_type=0 and b.relate_id is null and a.department_id in (158,155)
//SQL;
//        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        foreach ($userList as $user){
            if(isset($privList[$user['id']])){
                continue;
            }
            $user['name'] = $user['username'];
            $user['user_id'] = $user['id'];
            $dingtalkUser = DingtalkUser::findOneByWhere(['kael_id'=>$user['id']]);
            $user['base_name'] = $dingtalkUser['base_name'] ?? '';

            $deptList = [];

            if(!empty($dingtalkUser)){
                $mainDeptId = $dingtalkUser['department_id'];
                if($dingtalkUser['corp_type'] == 2 && $mainDeptId == 1){
                    $mainDeptId = 2;
                }
                $dingtalkDepaertmentUser = DingtalkDepartmentUser::findList(['kael_id'=>$user['id']]);
                $deptIds = array_values(array_unique(array_column($dingtalkDepaertmentUser,'department_id')));
                !in_array($mainDeptId,$deptIds) && $deptIds[] = $mainDeptId;
                $deptListAll = DingtalkDepartment::findList(['id'=>$deptIds],'','id,name,path_name_ding,path_id');
                foreach ($deptListAll as $v){
                    $deptList[] = [
                        'id'=>$v['id'],
                        'name'=>$v['name'],
                        'path_name'=>$v['path_name_ding'],
                        'path_id'=>$v['path_id'],
                        'is_main'=>$v['id'] == $mainDeptId ? 1 : 0,
                    ];
                }
                if($mainDeptId == 1){
                    $deptList[] = [
                        'id'=>1,
                        'name'=>'小盒科技',
                        'path_name'=>'小盒科技',
                        'path_id'=>'|1|',
                        'is_main'=>1,
                    ];
                }elseif($mainDeptId == 2){
                    $deptList[] = [
                        'id'=>2,
                        'name'=>'兼职辅导',
                        'path_name'=>'兼职辅导',
                        'path_id'=>'|2|',
                        'is_main'=>1,
                    ];
                }
            }


            $user['dept_list'] = $deptList;


            $headers = [
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*",
                "Content-Type: application/json",
            ];
            echo json_encode($user,64|256)."\n";
            $userId = $user['id'];
            $user = json_encode($user,64|256);
            $ret = AppFunc::curlPost(Yii::$app->params['boss_url'].'/employee/employeeValidateForKael.do',$user,$headers);
//            $ret = AppFunc::curlPost('https://beta-bslive.knowbox.cn/employee/employeeValidateForKael.do',$user,$headers);
            echo $ret."\n";
            $oldPlatIds = RelateUserPlatform::find()
                ->select('platform_id')
                ->where(['user_id'=>$userId,'status'=>0])->asArray(true)->column();
            $addPlat = array_values(array_diff([50004,50005],$oldPlatIds));
            !empty($addPlat) && RelateUserPlatform::batchAdd($userId,$addPlat);
            //sleep(1);
        }
    }


    public function actionBossTest(){
        if(exec('ps -ef|grep "priv/boss-test"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $sql = <<<SQL
select DISTINCT a.*
from `user` a
where a.`status` = 0 and a.user_type=0 and a.id=59021 and a.department_id in (158,155)
SQL;
        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        foreach ($userList as $user){
            $user['name'] = $user['username'];
            $user['user_id'] = $user['id'];

            $headers = [
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*",
                "Content-Type: application/json",
            ];
            echo json_encode($user,64|256)."\n";
            $user = json_encode($user,64|256);
            $ret = AppFunc::curlPost(Yii::$app->params['boss_url'].'/employee/employeeValidateForKael.do',$user,$headers);
//            $ret = AppFunc::curlPost('https://beta-bslive.knowbox.cn/employee/employeeValidateForKael.do',$user,$headers);
            echo $ret."\n";
            //sleep(1);
        }
    }

    //离职状态
    public function actionBossEmployee(){
        $kaelIds = LiveEmployee::find()
            ->select('kael_id')
            ->where(['status'=>[1,3]]) // 1 在职 2 离职 3 将离职
            ->andWhere('kael_id > 0')
            ->asArray(true)
            ->column();
        $kaelIdsDels = LiveEmployee::find()
            ->select('kael_id')
            ->where(['status'=>[2]]) // 1 在职 2 离职 3 将离职
            ->andWhere('kael_id > 0')
            ->asArray(true)
            ->column();
        $kaelIds = array_values(array_unique($kaelIds));
        $kaelIdsDels = array_values(array_unique(array_diff($kaelIdsDels,$kaelIds)));
        $validKaelIds = CommonUser::find()
            ->select('id')
            ->where(['status'=>0])
            ->asArray(true)
            ->column();

        $needDelIds = array_diff($kaelIds,$validKaelIds);
        $needReIds = array_intersect($kaelIdsDels,$validKaelIds);
        foreach ($needDelIds as $delId) {
            echo "del: $delId\n";
       //     BossApi::employeeUpdateJobStatus($delId,2);
        }
        foreach ($needReIds as $reId){
            echo "re: $reId\n";
        //    BossApi::employeeUpdateJobStatus($reId,1);
        }
    }
}
