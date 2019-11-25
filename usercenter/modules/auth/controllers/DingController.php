<?php
namespace usercenter\modules\auth\controllers;

use common\models\DingtalkDepartment;
use common\models\DingtalkUser;
use usercenter\components\exception\Exception;
use usercenter\modules\auth\models\Api;
use usercenter\controllers\BaseController;

require_once (__DIR__ . '/../config/constant.php');

class DingController extends BaseController
{


    public function actionDeptByKaelList(){
        try{
            $kaelIds = \Yii::$app->request->post('kael_id_list',0);
            if(empty($kaelIds)){
                throw new Exception("kael_id_list参数未传");
            }
            if(!is_array($kaelIds)){
                throw new Exception("参数格式错误");
            }
            $dingtalkUserList = DingtalkUser::find()
                ->select('kael_id,department_id,corp_type')
                ->where(['kael_id'=>$kaelIds])
                ->orderBy('status asc,auto_id desc')
                ->asArray(true)->all();
            $departmentListIndex = [];
            foreach ($dingtalkUserList as $v){
                if(!isset($departmentListIndex[$v['kael_id']])){
                    $departmentListIndex[$v['kael_id']] = $v;
                }
            }
            $departmentIds = array_unique(array_column($dingtalkUserList,'department_id'));
            $departmentList = DingtalkDepartment::find()
                ->select('id,path_name')
                ->where(['id'=>$departmentIds])
                ->asArray(true)->all();
            $departmentIdToPathName = array_column($departmentList,'path_name','id');
            $retList = [];
            foreach ($kaelIds  as $kaelIdOne){
                $dingUserInfo = $departmentListIndex[$kaelIdOne] ?? [];
                if(empty($dingUserInfo)){
                    $retList[] = [
                        'kael_id'=>$kaelIdOne,
                        'dept_1'=>'',
                        'dept_2'=>'',
                        'dept_3'=>'',
                        'dept_4'=>'',
                        'dept_5'=>'',
                        'dept_6'=>'',
                    ];
                }elseif($dingUserInfo['department_id'] == 1){
                    $retList[] = [
                        'kael_id'=>$kaelIdOne,
                        'dept_1'=>$dingUserInfo['corp_type'] == 2 ? "知识印象" : '小盒科技',
                        'dept_2'=>'',
                        'dept_3'=>'',
                        'dept_4'=>'',
                        'dept_5'=>'',
                        'dept_6'=>'',
                    ];
                }else{
                    $pathName = $departmentIdToPathName[$dingUserInfo['department_id']] ?? '';
                    $pathNameArr = explode('/',$pathName);
                    $retList[] = [
                        'kael_id'=>$kaelIdOne,
                        'dept_1'=>$pathNameArr[0]??'',
                        'dept_2'=>$pathNameArr[1]??'',
                        'dept_3'=>$pathNameArr[2]??'',
                        'dept_4'=>$pathNameArr[3]??'',
                        'dept_5'=>$pathNameArr[4]??'',
                        'dept_6'=>$pathNameArr[5]??'',
                    ];
                }

            }
            return $this->success(['list'=>$retList]);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

}