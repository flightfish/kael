<?php
namespace console\controllers;

use common\libs\ydd\Ydd;
use common\models\CommonUser;
use Yii;
use yii\console\Controller;


class YinddController extends Controller
{
    /**
     * 初始化用户信息到Meican
     */
    public function actionUpdate(){
        if(exec('ps -ef|grep "yindd/update"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }
        if(empty(Yii::$app->params['ydd_appkey'])){
            echo "未设置印点点信息\n";
            exit();
        }
        //全部部门
        $list = Ydd::depList();
        var_dump($list);
        $list2 = Ydd::userList();
        var_dump($list2);
        exit();
    }
}
