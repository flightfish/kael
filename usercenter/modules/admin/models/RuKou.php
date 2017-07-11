<?php

namespace questionmis\modules\admin\models;

use common\models\EntrystoreAuthUser;
use common\models\CommonModulesUser;
use questionmis\models\RequestBaseModel;
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
            'name' => '人员管理',
        ];
        $retData = [
            'list'=>$data,
        ];
        return $retData;
    }
}
