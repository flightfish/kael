<?php

namespace usercenter\models;

use common\libs\AES;
use common\libs\AppFunc;
use common\libs\Constant;
use common\libs\UserToken;
use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\modules\auth\models\UserModel;
require_once ('../modules/auth/config/constant.php');


class RequestBaseModel extends BaseModel
{

    const USER_TYPE_INNER = 0;

    static $_user = null;

    private $user;

    public $checkIp = true;

    public $token;

    public $auth_platform_id = 0;


    public function rules()
    {
        return [
            [['auth_platform_id'],'integer'],
//            [['token'], 'required'],
            ['token', 'string'],
        ];
    }

    /**
     * @return mixed
     */
    public function getUser(){
        //根据token获取userId
        if(!empty(self::$_user)) return self::$_user;
//        if(empty($this->token)){
//            $this->token = \Yii::$app->request->get('token',"");
//        }
//
//        $userId = $this->getUserIdByToken($this->token);
//
//        $user = UserCenter::findOneById($userId);
//        if(empty($user)){
//            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
//        }
//        $user['name'] = $user['username'];
//        $user['user_id'] = $user['id'];
        $token = UserToken::getToken();
        $this->token = UserToken::getToken();
        if(empty($token)){
            throw new Exception(Exception::NOT_LOGIN_MSG,Exception::NOT_LOGIN_CODE);
        }
        $user = UserToken::tokenToUser($token,$this->checkIp);
        self::$_user = $user;
        return self::$_user;
    }

    public function getUserIdByToken($token){
        if(empty($token)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        //解析token
        $token = str_replace(" ", "+", $token);
        $aes = new AES();
        $token = $aes->decode($token);
        $arrToken = explode('||', $token);
        if (count($arrToken) != 2) {
            return ['code'=>'-3','message'=>ERROR_FIND_NO_STUDENT];
        }
        $mobile = $arrToken[0];
        $passwd = $arrToken[1];
        $userModel = new UserModel();
        $itemUser = $userModel->getUserByMobile($mobile);
        if(empty($itemUser)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        if ($passwd != $itemUser['password']) {
            throw new Exception(Exception::MOBILE_CHANGE,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        $userId = intval($itemUser['id']);
        return $userId;
    }

    public function setUser($user){
        if(!empty($user)){
            self::$_user = $user;
        }
    }
}