<?php
namespace usercenter\modules\auth\controllers;

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
            $model = new Api(['scenario'=>Api::SCENARIO_WHERE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->getUserListPageByPlatformWhere();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

}