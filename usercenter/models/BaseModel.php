<?php

namespace usercenter\models;

use common\libs\Constant;
use common\libs\UserToken;
use usercenter\components\exception\Exception;
use yii\base\Model;


class BaseModel extends Model
{

    public $sourceLoadData;

   public function getErrorString(){
       $error = "";
       foreach($this->getFirstErrors() as $k=>$v){
           $error .= $v;
       }
       return $error;
   }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $ret  = parent::validate($attributeNames, $clearErrors);
        if(false === $ret){
            throw new Exception($this->getErrorString(),Exception::COMMON_ERROR_CODE_PARAM_VALIDATE_FAIL);
        }
        return $ret;
    }

    public function load($data, $formName = null)
    {
        if(!isset($data['auth_platform_id'])){
            $data['auth_platform_id'] = \Yii::$app->request->post('auth_platform_id',0);
        }

        $data['token'] = UserToken::getToken();

        $this->sourceLoadData = $data;
        if($formName === null){
            $data = ['dataForm'=>$data];
            $formName = 'dataForm';
        }
        return parent::load($data, $formName);
    }
}