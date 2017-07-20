<?php

namespace usercenter\modules\common\models;

use common\models\EntrystoreAuthUser;
use common\models\CommonModules;
use common\models\Platform;
use common\models\RelateUserPlatform;
use common\models\UserCenter;
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
        if($this->user['user_type'] != self::USER_TYPE_INNER){
            //非内部员工
            throw new Exception("权限不足",Exception::ERROR_COMMON);
        }
        $data = [];

        $relateList = RelateUserPlatform::findListByUserPlatform($this->user['id']);
        $platformIds = array_column($relateList,'platform_id');
        $platformList = Platform::findListById($platformIds);
        $ip = Yii::$app->request->getUserIP();

        foreach($platformList as $info){
            if(empty($info['platform_url'])){
                continue;
            }
            if(!empty($info['allow_ips'])){
                $allowIps = explode(',',$info['allow_ips']);
                if(!in_array($ip,$allowIps)){
                    continue;
                }
            }
            $data[] = [
                'url' => $info['platform_url'],
                'name' => $info['platform_name'],
            ];
        }
        $username[]['username'] = $this->user['username'];
        $retData = [
            'list'=>array_values($data),
            'username'=>$username
        ];
        return $retData;
    }
}
