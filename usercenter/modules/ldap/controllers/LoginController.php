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
            return json_encode($result, JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            return json_encode(['message'=>$e->getMessage(),'data'=>[]], JSON_UNESCAPED_UNICODE);

        }
    }
}