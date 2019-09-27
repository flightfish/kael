<?php

namespace usercenter\modules\meican\models;

use common\libs\DingTalkApi;
use common\models\DingtalkDepartment;
use usercenter\models\RequestBaseModel;

class ZzlLogin extends RequestBaseModel
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

    public function loginUrlByCode($code){
        $dingDbInfo = DingTalkApi::getDingDbInfoByCode($code,'zzl');
        $dingDepartmentInfo = DingtalkDepartment::findOneByWhere(['id'=>$dingDbInfo['department_subroot']]);
        if(empty($dingDepartmentInfog)){
            $deparmentName = '小盒科技';
        }else{
            $deparmentName = $dingDepartmentInfo['name'];
        }
        $url = ZzlApi::genLoginUrl($dingDbInfo['kael_id'],$dingDbInfo['name'],$deparmentName);
        return $url;
    }

}
