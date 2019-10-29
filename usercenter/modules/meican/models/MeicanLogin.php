<?php

namespace usercenter\modules\meican\models;

use common\libs\DingTalkApi;
use common\models\CommonUser;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class MeicanLogin extends RequestBaseModel
{

    const SCENARIO_LOGIN = "SCENARIO_LOGIN";

    public function rules()
    {
        return array_merge([

        ], parent::rules());
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = [];
        return $scenarios;
    }

    public function loginUrl(){
        if($this->user['user_type'] != 0){
            throw new Exception("非正式员工，权限不足",Exception::ERROR_COMMON);
        }
        $url = MeicanApi::genLoginUrl($this->user['user_id']);
        return $url;
    }

    public function loginUrlByCode($code){
        $dingUserInfo = DingTalkApi::getUserInfoByCode($code);
        $mobile = $dingUserInfo['mobile'];
        $userInfo = CommonUser::find()->where(['mobile'=>$mobile,'status'=>0,'user_type'=>0])->asArray(true)->one();
        if(empty($userInfo)){
            $userInfo = CommonUser::find()->where(['work_number'=>$dingUserInfo['jobnumber'],'status'=>0,'user_type'=>0])->asArray(true)->one();
        }
        if(empty($userInfo)){
            throw new Exception("KAEL用户不存在",Exception::ERROR_COMMON);
        }
        $url = MeicanApi::genLoginUrl($userInfo['id']);
        return $url;
    }

}
