<?php
namespace console\controllers;

use common\libs\AppFunc;
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
        RelateUserPlatform::getDb()->createCommand($sql)->execute();


    }
}
