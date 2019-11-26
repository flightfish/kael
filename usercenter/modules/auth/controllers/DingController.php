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

    public function actionDeptTree(){
        try{
            $deptList = DingtalkDepartment::findList([],'','id,corp_type,name,path_id,parentid');
            $deptList = array_column($deptList,null,'id');
            $retList = [
                1=>[
                    'id'=>1,
                    'name'=>'小盒科技',
                ],
                2=>[
                    'id'=>2,
                    'name'=>'兼职辅导',
                ]
            ];
            foreach ($deptList as &$v){
                $v['id'] = intval($v['id']);
                if($v['parentid'] == 1 && isset($retList[$v['corp_type']])){
                    $retList[$v['corp_type']]['children'][] = &$v;
                }elseif(isset($deptList[$v['parentid']])){
                    $deptList[$v['parentid']]['children'][] = &$v;
                }
                unset($v['corp_type']);
                unset($v['parentid']);
                unset($v['path_id']);
            }
            $retList = array_values($retList);
            $ret =  ['tree'=>$retList];
            return $this->success($ret);

        }catch (\Exception $exception){
            return $this->error($exception);
        }

    }

    public function actionDeptList(){
        try{
            $deptList = DingtalkDepartment::findList([],'','id,corp_type,name,parentid');
            $retList = [
                [
                    'id'=>1,
                    'name'=>'小盒科技',
                    'parentid'=>0
                ],
                [
                    'id'=>2,
                    'name'=>'兼职辅导',
                    'parentid'=>0
                ]
            ];
            foreach ($deptList as $v){
                if($v['parentid'] == 1){
                    $v['parentid'] = $v['corp_type'];
                }
                unset($v['corp_type']);
                $v['id'] = intval($v['id']);
                $v['parentid'] = intval($v['parentid']);
                $retList[] = $v;
            }
            $ret = ['list'=>$retList];
            return $this->success($ret);
        }catch (\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionDeptSubId(){
        try{
            $id = \Yii::$app->request->post('id',0);
            if(empty($id) || !is_numeric($id)){
                throw new Exception("参数错误");
            }
            $departmentIdsInPath = DingtalkDepartment::findListByWhereAndWhereArr([],[['like','path_id',"|{$id}|"]],'id');
            $subId = array_column($departmentIdsInPath,'id');
            return $this->success(['subid'=>$subId]);
        }catch (\Exception $exception){
            return $this->error($exception);
        }

    }
}