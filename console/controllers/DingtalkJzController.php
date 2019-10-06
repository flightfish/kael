<?php
namespace console\controllers;


use common\libs\DingTalkApiJZ;
use yii\console\Controller;


class DingtalkJzController extends Controller
{
    public function actionTest(){

        $departmentList = DingTalkApiJZ::getDepartmentAllList();
        $departmentList = array_column($departmentList,null,'id');
        foreach ($departmentList as $k=>$v){
            if(!empty($departmentList[$v['parentid']])){
                $parentInfo = $departmentList[$v['parentid']];
                if(!empty($departmentList[$parentInfo['parentid']])){
                    $v['name3'] = $v['name'] . '|' . $parentInfo['name'] . '|' . $departmentList[$parentInfo['parentid']]['name'];
                }else{
                    $v['name2'] = $v['name'] . '|' . $parentInfo['name'];
                }
                $departmentList[$k] = $v;
            }
        }
        $nameToInfo = [];
        foreach ($departmentList as $k=>$v){
            $name = $v['name3'] ?? $v['name2'] ?? $v['name'];
            $nameToInfo[$name] = $v;
        }
        echo json_encode($nameToInfo,64|256)."\n";
        return $departmentList;
    }
}
