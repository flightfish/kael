<?php

namespace usercenter\modules\admin\models;

use common\models\EntrystoreAuthUser;
use common\models\CommonModulesUser;
use common\models\Role;
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
        $data = [];
        $data[] = [
            'url' => '/admin/user/index',
            'name' => '平台用户管理',
        ];
        if($this->user['admin'] == Role::ROLE_ADMIN){
            //超级管理员
            $data[] = [
                'url'=>'/admin/department/index',
                'name' => '部门管理',
            ];
        }
        $retData = [
            'list'=>$data,
        ];
        return $retData;
    }
}
