<?php
namespace usercenter\modules\auth\controllers;

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
        return [
            'work_level'=>array_values(array_map(function($v){return ['id'=>$v['id'],'name'=>$v['name']];},$workLevelEntity)),
            'work_type'=>array_values(array_map(function($v){return ['id'=>$v['id'],'name'=>$v['name']];},$workTypeEntity)),
        ];
    }

}