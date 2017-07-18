<?php

namespace usercenter\models;

use common\libs\AES;
use common\libs\AppFunc;
use common\models\EntrystoreAuthUser;
use common\models\CurlApi;
use common\models\QselectAuthUser;
use common\models\QualitysysAuthUser;
use common\models\UserCenter;
use usercenter\components\exception\Exception;
use usercenter\modules\auth\models\UserModel;
use usercenter\modules\entrystore\user\models\UserApi;
require_once ('../modules/auth/config/constant.php');


class RequestBaseModel extends BaseModel
{

    const USER_TYPE_INNER = 0;

    static $_user = null;

    private $user;

    public $token;

    public function rules()
    {
        return [
            [['token'], 'required'],
            ['token', 'string'],
        ];
    }

    /**
     * @return mixed
     */
    public function getUser(){
        //根据token获取userId
        if(!empty(self::$_user)) return self::$_user;
        if(empty($this->token)){
            $this->token = \Yii::$app->request->get('token',"");
        }

        $userId = $this->getUserIdByToken($this->token);

        $user = UserCenter::findOneById($userId);
        if(empty($user)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        $user['name'] = $user['username'];
        $user['user_id'] = $user['id'];
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