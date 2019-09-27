<?php
namespace usercenter\modules\meican\controllers;

use usercenter\controllers\BaseController;
use usercenter\modules\meican\models\ZzlLogin;
use Yii;

//竹蒸笼
class LoginZzlController extends BaseController{

    public $enableCsrfValidation = false;


    public function actionDing(){
        try{
            $code = \Yii::$app->request->get('code','');
            $model = new ZzlLogin(['scenario'=>ZzlLogin::SCENARIO_LOGIN]);
            $url = $model->loginUrlByCode($code);
            $this->redirect($url);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }
}