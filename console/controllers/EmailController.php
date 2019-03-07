<?php
namespace console\controllers;

use common\libs\AppFunc;
use common\models\CommonUser;
use common\models\Department;
use Yii;
use yii\console\Controller;


class EmailController extends Controller
{
    /**
     * 初始化用户信息到LDAP
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "email/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }

    }
}
