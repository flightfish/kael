<?php

namespace usercenter\modules\admin\models;

use common\libs\UserToken;
use common\models\CommonModulesUser;
use common\models\Platform;
use common\models\Role;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class RuKou extends RequestBaseModel
{

    const SCENARIO_RUKOU = "SCENARIO_RUKOU";

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_RUKOU] = ['token'];

        return $scenarios;
    }

    public function rules()
    {
        return array_merge([
        ], parent::rules());
    }


    /**
     * 入口列表
     */
    public function RuKou()
    {
        $platformInfo = Platform::findOneById(1);
        $clientIPAllow = explode(',',$platformInfo['allow_ips']);
        $clientIP = UserToken::getRealIP(false);
        if(!empty($platformInfo['allow_ips']) && !in_array($clientIP,$clientIPAllow)){
            throw new Exception('无权访问，请联系管理员',Exception::ERROR_COMMON);
        }

        $data = [];

        if($this->user['admin'] == Role::ROLE_ADMIN){
            //超级管理员
            $data[] = [
                'url' => '/admin/user/index-user',
                'name' => '用户管理',
            ];
            $data[] = [
                'url' => '/admin/user/index-priv',
                'name' => '权限管理',
            ];
            $data[] = [
                'url'=>'/admin/department/index',
                'name' => '部门管理',
            ];
        }else{
            $data[] = [
                'url' => '/admin/user/index-priv',
                'name' => '权限管理',
            ];
        }
        $retData = [
            'list'=>$data,
        ];
        return $retData;
    }
}
