<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\libs\UserToken;
use common\models\CommonUser;
use common\models\Department;
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
where a.`status` = 0 and a.user_type=0 and b.relate_id > 0 and a.department_id=158
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
            $ret = AppFunc::curlPost('http://bslive.online.knowboxlan.cn/permissionsMenu.do',[],$headers);
            echo $ret."\n";
            sleep(1);
        }
    }


    public function actionBossAllNew(){
        if(exec('ps -ef|grep "priv/boss-all-new"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $sql = <<<SQL
select DISTINCT a.*
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=50004
where a.`status` = 0 and a.user_type=0 and b.relate_id > 0 and a.department_id=158
SQL;
        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        foreach ($userList as $user){
            $user['name'] = $user['username'];
            $user['user_id'] = $user['id'];

            $headers = [
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*"
            ];
            echo $user['id']."\n";
            $ret = AppFunc::curlPost('https://bslive.online.knowboxlan.cn/employee/employeeValidateForKael.do',$user,$headers);
            echo $ret."\n";
            sleep(1);
        }
    }

    public function actionBossUpdateNew(){
        if(exec('ps -ef|grep "priv/boss-update-new"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        $sql = <<<SQL
select DISTINCT a.*
from `user` a
 left join relate_user_platform b on a.id=b.user_id and b.platform_id=50004
where a.`status` = 0 and a.user_type=0 and b.relate_id is null and a.department_id=158
SQL;
        $userList = CommonUser::getDb()->createCommand($sql)->queryAll();

        foreach ($userList as $user){
            $user['name'] = $user['username'];
            $user['user_id'] = $user['id'];

            $headers = [
                "Referer: https://bslive.knowbox.cn/",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
                "Sec-Fetch-Mode: cors",
                "Accept: application/json, text/plain, */*"
            ];
            echo $user['id']."\n";
            $ret = AppFunc::curlPost('https://bslive.online.knowboxlan.cn/employee/employeeValidateForKael.do',$user,$headers);
            echo $ret."\n";
            $oldPlatIds = RelateUserPlatform::find()
                ->select('platform_id')
                ->where(['user_id'=>$user['id'],'status'=>0])->asArray(true)->column();
            $addPlat = array_values(array_diff([50004,50005],$oldPlatIds));
            !empty($addPlat) && RelateUserPlatform::batchAdd($user['id'],$addPlat);
            sleep(1);
        }
    }
}
