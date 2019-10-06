<?php
namespace console\controllers;


use common\libs\DingTalkApiJZ;
use yii\console\Controller;


class DingtalkJzController extends Controller
{
    public function actionTest(){

        $departmentList = DingTalkApiJZ::getDepartmentAllList();
        return $departmentList;
    }
}
