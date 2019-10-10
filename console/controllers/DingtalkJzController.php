<?php
namespace console\controllers;



use common\libs\DingTalkApi;
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

//        $filePath = './jianzhi_201909.xls';
        $filePath = './jianzhi_20191010.xls';
        $PHPReader = new \PHPExcel_Reader_Excel5();
        $objPHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $data = $objPHPExcel->getSheet(0)->toArray();
//        $dataFormat = [];
        $rows = [];
        $lastId = TmpImportJianzhi::find()->select('id')->orderBy('id desc')->limit(1)->asArray(true)->scalar();
        $lastId = intval($lastId);
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
            $lastId += 1;
            $tmp = ['id'=>$lastId,'name'=>$v[1],'mobile'=>$v[6],'department_name'=>"{$v[5]}|{$v[4]}|{$v[3]}|{$v[2]}"];
            $tmp['department_id'] = $nameToInfo[$tmp['department_name']]['id'];
            echo json_encode($tmp,64|256)."\n";
//            $dataFormat[] = $tmp;
            $rows[] = [$lastId,$tmp['mobile'],$tmp['name'],$tmp['department_name'],$tmp['department_id']];
        }
//        echo json_encode($rows,64|256)."\n";
//        exit();
        $columns = ['id','mobile','name','department_name','department_id'];
        DBCommon::batchInsertAll(TmpImportJianzhi::tableName(),$columns,$rows,TmpImportJianzhi::getDb(),'INSERT IGNORE');

//        echo json_encode($dataFormat,64|256)."\n";

//        return $dataFormat;
    }


    private function actionImportFromRows(){
        exit();
        $rows = trim(file_get_contents('/data/wwwroot/kael/rows.json'));
        $rows = json_decode($rows,true);
        $columns = ['mobile','name','department_name','department_id'];
        DBCommon::batchInsertAll(TmpImportJianzhi::tableName(),$columns,$rows,TmpImportJianzhi::getDb(),'INSERT IGNORE');
    }

    public function computeWorknumber(){
        $list = TmpImportJianzhi::find()->where(['status'=>0,'work_number'=>''])
            ->asArray(true)->all();
        foreach ($list as $v){
            if(!empty($v['work_number'])){
                continue;
            }
            $workNunmber = '';
            $orgId = intval($v['id']);
            $v['id'] = intval($v['id']);
            while($v['id']){
                $next = $v['id']%9 + 1;
                $v['id'] = intval($v['id']/9);
                $workNunmber = $next.$workNunmber;
            }
            $workNunmber = 1000000 + intval($workNunmber);
            $workNunmber = 'JZ'.$workNunmber;
            TmpImportJianzhi::updateAll(['work_number'=>$workNunmber],['id'=>$orgId]);
        }
    }


    private function actionExportFileFromTmp(){
        exit();
        $list = TmpImportJianzhi::find()->where(['status'=>0])->asArray(true)->all();
        $str = json_encode($list,64|256);
        file_put_contents('/data/wwwroot/kael/tmpimport.json',$str);
    }


    private function actionImportDingFromFile(){
        exit();
        //$list = TmpImportJianzhi::find()->where(['status'=>0])->asArray(true)->all();
        $list = file_get_contents('/data/wwwroot/kael/tmpimport.json');
        $list = json_decode($list,true);
        $listNew = $list;
        foreach ($list as $k=>$v){
            if(!empty($v['ding_userid'])){
               continue;
            }
            if(empty($v['work_number'])){
                continue;
            }
            echo $v['work_number'].'-'.$v['name'].'-'.$v['mobile'].'-'.$v['department_id'].'-'.$v['department_name']."\n";
            try{
                DingTalkApiJZ::addUser($v['work_number'],$v['name'],$v['mobile'],$v['department_id'],$v['work_number']);
                $listNew[$k]['ding_userid'] = $v['work_number'];
            }catch (\Exception $e){
                $listNew[$k]['ding_userid'] = '-1';
                $listNew[$k]['ding_error'] = $e->getMessage();
                echo "error: ".$e->getMessage()."\n";
            }
            file_put_contents('/data/wwwroot/kael/tmpimport.json',json_encode($listNew,64|256));
//            sleep(1);
//            break;
        }
    }

    private function actionCheckImport(){
        exit();
        $list = file_get_contents('/data/wwwroot/kael/tmpimport.json');
        $list = json_decode($list,true);
        foreach ($list as $k=>$v){
            if(!empty($v['ding_userid'])){
                continue;
            }
            if(empty($v['work_number'])){
                continue;
            }
            if($v['ding_userid'] != '-1'){
                continue;
            }
            echo $v['work_number'].'-'.$v['name'].'-'.$v['mobile'].'-'.$v['department_id'].'-'.$v['department_name'].'-'.$v['ding_error']."\n";
        }
    }

    private function actionRestore(){
        exit();
        $list = file_get_contents('/data/wwwroot/kael/tmpimport.json');
        $list = json_decode($list,true);
        /**
        `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
        `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '电话号码',
        `name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
        `department_name` varchar(255) NOT NULL DEFAULT '' COMMENT '部门名称',
        `department_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '部门id',
        `ding_userid` varchar(100) NOT NULL DEFAULT '',
        `ding_error` varchar(1000) NOT NULL DEFAULT '',
        `work_number` varchar(100) NOT NULL DEFAULT '',
         */
        $columns = ['id','mobile','name','department_name','department_id','ding_userid','ding_error','work_number'];
        $rows = [];
        foreach ($list as $k=>$v){
            $rows[] = [
                $v['id'],
                $v['mobile'],
                $v['name'],
                $v['department_name'],
                $v['department_id'],
                $v['ding_userid'],
                $v['ding_error']??'',
                $v['work_number']
            ];
        }
        DBCommon::batchInsertAll(TmpImportJianzhi::tableName(),$columns,$rows,TmpImportJianzhi::getDb(),'INSERT IGNORE');
    }

    private function actionImportDing(){
        exit();
        echo "update compute worknumber\n";
        $this->computeWorknumber();
        sleep(1);
        echo "select need import\n";
        $list = TmpImportJianzhi::find()->where(['status'=>0,'ding_userid'=>['','0','-1']])->asArray(true)->all();
        $listNew = $list;
        foreach ($list as $k=>$v){
            if(empty($v['work_number'])){
                continue;
            }
            echo $v['work_number'].'-'.$v['name'].'-'.$v['mobile'].'-'.$v['department_id'].'-'.$v['department_name']."\n";
            try{
                DingTalkApiJZ::addUser($v['work_number'],$v['name'],$v['mobile'],$v['department_id'],$v['work_number']);
                $listNew[$k]['ding_userid'] = $v['work_number'];
                TmpImportJianzhi::updateAll(['ding_userid'=>$v['work_number']],['id'=>$v['id']]);
                echo "success\n";
            }catch (\Exception $e){
                $listNew[$k]['ding_userid'] = '-1';
                $listNew[$k]['ding_error'] = $e->getMessage();
                echo "error: ".$e->getMessage()."\n";
                TmpImportJianzhi::updateAll(['ding_userid'=>'-1','ding_error'=>$e->getMessage()],['id'=>$v['id']]);
            }
//            file_put_contents('/data/wwwroot/kael/tmpimport.json',json_encode($listNew,64|256));
        }
    }

    public function actionImportDingPre(){
        echo "update compute worknumber\n";
        $this->computeWorknumber();
        sleep(1);
        echo "select need import pre\n";
        $list = TmpImportJianzhi::find()->where(['status'=>0,'ding_userid'=>''])->andWhere(['>=','create_time','2019-10-10 17:00:00'])->asArray(true)->all();
        $listNew = $list;
        foreach ($list as $k=>$v){
            if(empty($v['work_number'])){
                continue;
            }
            echo $v['work_number'].'-'.$v['name'].'-'.$v['mobile'].'-'.$v['department_id'].'-'.$v['department_name']."\n";
            try{
                DingTalkApiJZ::addPreEntry($v['name'],$v['mobile'],$v['department_id'],$v['work_number']);
                $listNew[$k]['ding_userid'] = 'PRE';
                TmpImportJianzhi::updateAll(['ding_userid'=>'PRE'],['id'=>$v['id']]);
                echo "success\n";
            }catch (\Exception $e){
                $listNew[$k]['ding_userid'] = 'PRE_ERR';
                $listNew[$k]['ding_error'] = $e->getMessage();
                echo "error: ".$e->getMessage()."\n";
                TmpImportJianzhi::updateAll(['ding_userid'=>'PRE_ERR','ding_error'=>$e->getMessage()],['id'=>$v['id']]);
            }
        }
    }
}
