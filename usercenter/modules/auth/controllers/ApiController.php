<?php
namespace usercenter\modules\auth\controllers;

use common\libs\UserToken;
use common\models\CommonUser;
use common\models\Department;
use common\models\WorkLevel;
use common\models\WorkType;
use usercenter\modules\auth\models\Api;
use usercenter\modules\auth\models\CommonApi;
use usercenter\controllers\BaseController;
use Yii;
use yii\web\Controller;

require_once (__DIR__ . '/../config/constant.php');

class ApiController extends BaseController
{

    public function actionSendSmsToMobile()
    {
        try{
            $model = new Api(['scenario'=>Api::SCENARIO_SENDSMSTTOMOBILE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->sendSmsToMobile();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }


    public function actionSendSms()
    {
        try{
            $model = new Api(['scenario'=>Api::SCENARIO_SENDSMS]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->sendSms();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }


    public function actionUserListByPlatformWhere(){
        try{
            $model = new Api(['scenario'=>Api::SCENARIO_WHERE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->getUserListByPlatformWhere();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionUserListByWhere(){
        try{
            $model = new Api(['scenario'=>Api::SCENARIO_WHERE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->getUserListByWhere();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionUserListPageByPlatformWhere(){
        try{
            $model = new Api(['scenario'=>Api::SCENARIO_WHERE_PAGE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->getUserListPageByPlatformWhere();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    /**
     * 工种&类型
     */
    public function actionWorkInfo(){
        $workLevelEntity = WorkLevel::findAllList();
        $workTypeEntity = WorkType::findAllList();
        $departmentEntity = Department::findAllList();
        return [
            'work_level'=>array_values(array_map(function($v){return ['id'=>$v['id'],'name'=>$v['name']];},$workLevelEntity)),
            'work_type'=>array_values(array_map(function($v){return ['id'=>$v['id'],'name'=>$v['name']];},$workTypeEntity)),
            'department'=>array_values(array_map(function($v){return ['id'=>$v['department_id'],'name'=>$v['department_name']];},$departmentEntity)),
        ];
    }

}