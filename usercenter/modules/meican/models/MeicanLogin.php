<?php

namespace usercenter\modules\meican\models;

use common\libs\Constant;
use common\libs\UserToken;
use common\models\CommonModulesUser;
use common\models\Department;
use common\models\LogAuthUser;
use common\models\Platform;
use common\models\RelateAdminDepartment;
use common\models\RelateDepartmentPlatform;
use common\models\RelateUserPlatform;
use common\models\Role;
use common\models\UserCenter;
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

}
