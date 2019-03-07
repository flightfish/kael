<?php
namespace usercenter\modules\meican\controllers;

use usercenter\components\exception\Exception;
use usercenter\controllers\BaseController;
use usercenter\models\RequestBaseModel;
use usercenter\modules\meican\models\MeicanApi;
use usercenter\modules\meican\models\MeicanLogin;
use Yii;

class QueryController extends BaseController{

    public $enableCsrfValidation = false;


    public function actionList()
    {
        try{
            $model = new RequestBaseModel();
            $model->load($this->loadData);
            $userInfo = $model->getUser();
            if(!empty($userInfo['user_type'])){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            if($userInfo['user_id'] != 11090){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            $list = MeicanApi::listMember();
            return $this->success($list);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }


    public function actionBill()
    {
        try{
            $model = new RequestBaseModel();
            $model->load($this->loadData);
            $userInfo = $model->getUser();
            if(!empty($userInfo['user_type'])){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            if($userInfo['user_id'] != 11090){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            $day = Yii::$app->request->get('day','');
            $list = MeicanApi::listBill($day);
            return $this->success($list);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }


    public function actionRule()
    {
        try{
            $model = new RequestBaseModel();
            $model->load($this->loadData);
            $userInfo = $model->getUser();
            if(!empty($userInfo['user_type'])){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            if($userInfo['user_id'] != 11090){
                throw new Exception("权限不足",Exception::ERROR_COMMON);
            }
            $list = MeicanApi::listRule();
            return $this->success($list);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }
}