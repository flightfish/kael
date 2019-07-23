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
        $ret = Ydd::depList();
        $retJson = json_decode($ret,true);
        if(empty($retJson) || !isset($retJson['status']) || $retJson['status'] != 8000){
            echo "获取部门列表失败\n";
            echo strval($ret)  . "\n";
        }
        /**
         * [{
        "id": 8123,
        "parentId": null,
        "name": "合伙人",
        "level": 0
        }]
         */
        $yinddDepartmentList = $retJson['data'];
        $list2 = Ydd::userList();
        var_dump($list2);
        exit();
    }
}
