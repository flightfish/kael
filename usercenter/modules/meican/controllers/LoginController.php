<?php
namespace usercenter\modules\meican\controllers;

use usercenter\controllers\BaseController;
use usercenter\modules\meican\models\MeicanLogin;
use Yii;

class LoginController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try{
            $model = new MeicanLogin(['scenario'=>MeicanLogin::SCENARIO_LOGIN]);
            $model->load($this->loadData);
            $model->validate();
            $url = $model->loginUrl();
            $this->redirect($url);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }
}