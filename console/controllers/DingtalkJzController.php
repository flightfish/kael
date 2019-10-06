<?php
namespace console\controllers;


use common\libs\DingTalkApiJZ;
use common\models\DBCommon;
use common\models\TmpImportJianzhi;
use yii\console\Controller;


class DingtalkJzController extends Controller
{
    public function actionImport(){

//        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
//        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。注意，这里的return是Yii框架的方式。
//            $PHPReader = new \PHPExcel_Reader_Excel5();
//            if (!$PHPReader->canRead($filePath)) {
//                $errorMessage = "Can not read file.";
//                array_push($error, $errorMessage);
//                return $error;
//            }
//        }



        $departmentList = DingTalkApiJZ::getDepartmentAllList();
        $departmentList = array_column($departmentList,null,'id');
        foreach ($departmentList as $k=>$v){
            if(!empty($departmentList[$v['parentid']])){
                $parentInfo = $departmentList[$v['parentid']];
                if(!empty($departmentList[$parentInfo['parentid']])){
                    $parentInfo2 = $departmentList[$parentInfo['parentid']];
                    if(!empty($departmentList[$parentInfo2['parentid']])){
                        $v['name4'] = $v['name'] . '|' . $parentInfo['name'] . '|' . $parentInfo2['name'] . '|' .$departmentList[$parentInfo2['parentid']]['name'];
                    }else{
                        $v['name3'] = $v['name'] . '|' . $parentInfo['name'] . '|' . $parentInfo2['name'];
                    }
                }else{
                    $v['name2'] = $v['name'] . '|' . $parentInfo['name'];
                }
                $departmentList[$k] = $v;
            }
        }
        $nameToInfo = [];
        foreach ($departmentList as $k=>$v){
            $name = $v['name4'] ?? $v['name3'] ?? $v['name2'] ?? $v['name'];
            $nameToInfo[$name] = $v;
        }

        $filePath = './jianzhi_201909.xls';
        $PHPReader = new \PHPExcel_Reader_Excel5();
        $objPHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $data = $objPHPExcel->getSheet(0)->toArray();
        $dataFormat = [];
        $rows = [];
        foreach ($data as $k=>$v){
            if($k == 0){
//                echo json_encode($v,64|256);
                continue;
            }
            if(empty($v[6])){
                //没有手机号
                continue;
            }
            //0工号忽略 1姓名 2.3.4.5 1-4级部门 6手机 7邮箱
            $tmp = ['name'=>$v[1],'mobile'=>$v[6],'department_name'=>"{$v[5]}|{$v[4]}|{$v[3]}|{$v[2]}"];
            $tmp['department_id'] = $nameToInfo[$tmp['department_name']]['id'];
            $dataFormat[] = $tmp;
            $rows[] = [$tmp['mobile'],$tmp['name'],$tmp['department_name'],$tmp['department_id']];
        }
        echo json_encode($rows,64|256)."\n";
        exit();
        $columns = ['mobile','name','department_name','department_id'];
        DBCommon::batchInsertAll(TmpImportJianzhi::tableName(),$columns,$rows,TmpImportJianzhi::getDb(),'INSERT IGNORE');

//        echo json_encode($dataFormat,64|256)."\n";

        return $dataFormat;
    }


    public function actionImportFromRows(){
        $rows = trim(file_get_contents('/data/wwwroot/kael/rows.json'));
        $rows = json_decode($rows,true);
        $columns = ['mobile','name','department_name','department_id'];
        DBCommon::batchInsertAll(TmpImportJianzhi::tableName(),$columns,$rows,TmpImportJianzhi::getDb(),'INSERT IGNORE');
    }
}
