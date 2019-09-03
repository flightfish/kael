<?php

namespace usercenter\modules\ldap\models;

use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;

class LdapLogin extends RequestBaseModel
{

    const SCENARIO_LOGIN = "SCENARIO_LOGIN";

    public $username;
    public $password;

    public function rules()
    {
        return array_merge([
            [['password','username'],'string'],
            [['password','username'],'required','on'=>self::SCENARIO_LOGIN],
        ], parent::rules());
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = [];
        return $scenarios;
    }

    public function login(){
        if(empty(trim($this->username))){
            throw new Exception("用户不存在");
        }
        $userInfo = UserCenter::findOneByWhere(['mobile'=>trim($this->username)]);
        if(empty($userInfo)){
            throw new Exception("用户不存在");
        }
        if($userInfo['password'] != md5($this->password)){
            throw new Exception("用户名或密码错误");
        }
        if($userInfo['user_type'] != 0){
            throw new Exception("非正式员工，权限不足",Exception::ERROR_COMMON);
        }
        return [
            'given_name' => $userInfo['username'],
            'mobile' => $userInfo['mobile'],
            'phone' => $userInfo['mobile'],
            'email' => $userInfo['email'],
            'department' => '小盒科技',
            'position' => '小盒科技',
            'location' => '小盒科技',
            'im' => '小盒科技',
        ];
    }


}
