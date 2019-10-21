<?php

namespace usercenter\modules\common\models;

use common\libs\UserToken;
use common\models\Platform;
use common\models\RelateDepartmentPlatform;
use common\models\RelateUserPlatform;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class RuKou extends RequestBaseModel
{

    const SCENARIO_RUKOU = "SCENARIO_RUKOU";
    const SCENARIO_LOGIN_PLATFORM = "SCENARIO_LOGIN_PLATFORM";

    public $platform_id;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_RUKOU] = ['token'];
        $scenarios[self::SCENARIO_LOGIN_PLATFORM] = ['token','platform_id'];
        return $scenarios;
    }

    public function rules()
    {
        return array_merge([
            [['platform_id'],'integer'],
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

        //密码权限设置
        if($this->user['user_type'] == 0){
            if(empty($this->user['password']) || $this->user['password'] == md5('123456')){
//                throw new Exception("密码过于简单，请修改密码后重试", Exception::ERROR_COMMON);
            }
        }

        $data = [];
        $deparmentPlatList = RelateDepartmentPlatform::findListByDepartment($this->user['department_id']);
        $platformIdsDepartment = array_column($deparmentPlatList,'platform_id');
        $relateList = RelateUserPlatform::findListByUserPlatform($this->user['id'],-1);
        $platformIds = array_column($relateList,'platform_id');
        $platformIds = array_intersect($platformIds,$platformIdsDepartment);
        $platformList = Platform::findListById($platformIds);
        $ip = UserToken::getRealIP(false);;


        usort($relateList,function($a,$b){
            return ($a['login_time'] == $b['login_time']) ? 0 :(($a['login_time'] < $b['login_time']) ? 1 : -1);
        });


        foreach($relateList as $relateInfo){
            $platformId = $relateInfo['platform_id'];
            if(empty($platformList[$platformId])){
                continue;
            }
            $info = $platformList[$platformId];
            if(empty($info['platform_url'])){
                continue;
            }
            if(empty($info['is_show'])){
                continue;
            }
            if(!empty($info['allow_ips'])){
                !empty($info['allow_ips']) && $info['allow_ips'] = $info['allow_ips'].','.(Yii::$app->params['allow_ips']);
                $allowIps = explode(',',$info['allow_ips']);
                if(!in_array($ip,$allowIps)){
                    continue;
                }
            }
            $data[$info['platform_id']] = [
                'url'=>'/common/welcome/login-platform?platform_id='.$info['platform_id'],
//                'url' => $info['platform_url'],// ."?token=".$this->token,
                'name' => $info['platform_name'],
                'icon' => $info['platform_icon']
            ];
        }
        if($this->user['user_type'] == 0&&!isset($data[6000])){
            $platform = Platform::findOneById(6000);
            $data[] = [
                'url'=>'/common/welcome/login-platform?platform_id=6000',
                'name' => $platform['platform_name'],
                'icon' => $platform['platform_icon']
            ];
        }

        $data = array_values($data);
        $username[]['username'] = $this->user['username'];




        $retData = [
            'list'=>array_values($data),
            'username'=>$username
        ];
        return $retData;
    }

    public function platformUrlWithLog(){
        if($this->user['user_type'] != self::USER_TYPE_INNER){
            //非内部员工
            throw new Exception("此平台仅限公司内部员工访问",Exception::ERROR_COMMON);
        }
        $relateList = RelateUserPlatform::findListByUserPlatform($this->user['id'],$this->platform_id);
        if(empty($relateList)){
            throw new Exception("权限不足",Exception::ERROR_COMMON);
        }
        $platformInfo = Platform::findOneById($this->platform_id);
        if(empty($platformInfo['platform_url'])){
            throw new Exception("此平台不存在",Exception::ERROR_COMMON);
        }
        if(empty($platformInfo['is_show'])){
            throw new Exception("系统不存在",Exception::ERROR_COMMON);
        }
        $ip = UserToken::getRealIP(false);;
        if(!empty($platformInfo['allow_ips'])){
            !empty($platformInfo['allow_ips']) && $platformInfo['allow_ips'] = $platformInfo['allow_ips'].','.(Yii::$app->params['allow_ips']);
            $allowIps = explode(',',$platformInfo['allow_ips']);
            if(!in_array($ip,$allowIps)){
                throw new Exception("此平台仅限公司内网访问",Exception::ERROR_COMMON);
            }
        }
        RelateUserPlatform::updateAll(
            ['login_time'=>date('Y-m-d H:i:s')],
            [
                'user_id'=>$this->user['id'],
                'platform_id'=>$this->platform_id,
                'status'=>RelateUserPlatform::STATUS_VALID
            ]);
        return $platformInfo['platform_url'];
    }


    public function ruKouAll()
    {
        if($this->user['user_type'] != self::USER_TYPE_INNER){
            //非内部员工
            throw new Exception("此平台仅限公司内部员工访问",Exception::ERROR_COMMON);
        }

        //密码权限设置
        if($this->user['user_type'] == 0){
            if(empty($this->user['password']) || $this->user['password'] == md5('123456')){
//                throw new Exception("密码过于简单，请修改密码后重试", Exception::ERROR_COMMON);
            }
        }

        $data = [];
        $deparmentPlatList = RelateDepartmentPlatform::findListByDepartment($this->user['department_id']);
        $platformIdsDepartment = array_column($deparmentPlatList,'platform_id');
        $relateList = RelateUserPlatform::findListByUserPlatform($this->user['id'],-1);
        $platformIds = array_column($relateList,'platform_id');
        $platformIds = array_intersect($platformIds,$platformIdsDepartment);
        $platformList = Platform::findListById($platformIds);
        $ip = UserToken::getRealIP(false);;


        usort($relateList,function($a,$b){
            return ($a['login_time'] == $b['login_time']) ? 0 :(($a['login_time'] < $b['login_time']) ? 1 : -1);
        });

        $data = [];

        $hasIpLimit = 0;
        foreach($relateList as $relateInfo){
            $platformId = $relateInfo['platform_id'];
            if(empty($platformList[$platformId])){
                continue;
            }
            $info = $platformList[$platformId];
            if(empty($info['platform_url'])){
                continue;
            }
            if(empty($info['is_show'])){
                continue;
            }
            $isIpLimit = 0;
            if(!empty($info['allow_ips'])){
                !empty($info['allow_ips']) && $info['allow_ips'] = $info['allow_ips'].','.(Yii::$app->params['allow_ips']);
                $allowIps = array_filter(explode(',',$info['allow_ips']));
                if(!in_array($ip,$allowIps)){
                    $isIpLimit = 1;
                    $hasIpLimit = 1;
                }
            }
            $data[$info['platform_id']] = [
                'url'=>$isIpLimit ? '' : '/common/welcome/login-platform?platform_id='.$info['platform_id'],
                'name' => $info['platform_name'],
                'icon' => $info['platform_icon'],
                'ip_limit'=>$isIpLimit
            ];
        }

        $data = array_values($data);

        $retData = [
            'list'=>array_values($data),
            'username'=>$this->user['username'],
            'ip_limit'=> $hasIpLimit,
            'ip'=>$ip
        ];
        return $retData;
    }
}
