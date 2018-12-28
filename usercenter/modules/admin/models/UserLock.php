<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2016/10/21
 * Time: 15:11
 */

namespace usercenter\modules\admin\models;

use common\libs\Cache;
use common\models\UserCenter;
use usercenter\models\RequestBaseModel;

class UserLock extends RequestBaseModel
{

    public $user_mobile;
    public $page;
    public $pagesize;


    const SCENARIO_UNLOCK = "SCENARIO_UNLOCK";
    const SCENARIO_LIST = "SCENARIO_LIST";

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UNLOCK] = ['user_mobile'];
        $scenarios[self::SCENARIO_LIST] = ['user_mobile','page','pagesize'];
        return $scenarios;
    }

    public function rules()
    {
        return array_merge([
            [['user_mobile'], 'required', 'on' => self::SCENARIO_UNLOCK],
            [['user_mobile'], 'required', 'on' => self::SCENARIO_LIST],
        ], parent::rules());
    }

    /**
     * 解锁
     */
    public function lockList()
    {
        $status = '正常';
        $cacheKey = ['kael_deepblue_user_mobile',$this->user_mobile];
        $checkCount = Cache::checkCache($cacheKey);
        $checkRes = isset($checkCount['count']) ? $checkCount['count'] : 0;
        if ($checkCount && $checkRes >= 3) {
            if ($checkRes < 10) {
                $status = '冷却中';
            } else {
                $status = '封禁';
            }
        }
        $where = [];

        $userList = UserCenter::findUserSearch($this->page,$this->pagesize,$this->user_mobile,$where);
        $total  = UserCenter::findUserSearchCount("id",$where);
        if (!empty($userList)) {
            foreach ($userList as $k => $v) {
                $userList[$k]['status'] = $status;
            }

        }

        $retData = [
            "total" => $total,
            "rows" => array_values($userList)
        ];

        return $retData;
    }

    /**
     * 解锁
     */
    public function userUnlock()
    {
        $cacheKey = ['kael_deepblue_user_mobile', $this->user_mobile];
        Cache::setCache($cacheKey, ['count' => 0]);
        return true;
    }
}