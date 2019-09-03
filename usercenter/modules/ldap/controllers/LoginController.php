<?php
namespace usercenter\modules\ldap\controllers;

use usercenter\controllers\BaseController;
use usercenter\modules\ldap\models\LdapLogin;
use Yii;

class LoginController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try{
            $model = new LdapLogin(['scenario'=>LdapLogin::SCENARIO_LOGIN]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->login();
            $result = [
                'message' => '',
                'data' => $data,
            ];
            return $result;
        }catch(\Exception $e){
            return ['message'=>$e->getMessage(),'data'=>[]];

        }
    }
}