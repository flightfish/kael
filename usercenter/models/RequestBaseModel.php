<?php

namespace questionmis\models;

use common\libs\AppFunc;
use common\models\EntrystoreAuthUser;
use common\models\CurlApi;
use common\models\QselectAuthUser;
use common\models\QualitysysAuthUser;
use common\models\UserCenter;
use questionmis\components\exception\Exception;
use questionmis\modules\entrystore\user\models\UserApi;


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
        //学科学段问题
        if($user['subject'] == -1){
            //未设置
            $subjectUser = QualitysysAuthUser::findOne($user['id']);
            if(empty($subjectUser)){
                $subjectUser = QselectAuthUser::findOne($user['id']);
            }
            if(!empty($subjectUser)){
                QualitysysAuthUser::updateAll(['subject'=>$subjectUser['subject'],'grade_part'=>$subjectUser['grade_part']],['user_id'=>$user['id']]);
                QselectAuthUser::updateAll(['subject'=>$subjectUser['subject'],'grade_part'=>$subjectUser['grade_part']],['user_id'=>$user['id']]);
                UserCenter::updateAll(['subject'=>$subjectUser['subject'],'grade_part'=>$subjectUser['grade_part']],['id'=>$user['id']]);
                $user['subject'] = $subjectUser['subject'];
                $user['grade_part'] = $subjectUser['grade_part'];
            }
        }
       self::$_user = $user;
        return self::$_user;
    }

    public function getUserIdByToken($token){
        if(empty($token)){
            throw new Exception(Exception::COMMON_USER_NOT_EXIST_MSG,Exception::COMMON_USER_NOT_EXIST_CODE);
        }
        //访问用户中心接口获取userId
        $userId = UserApi::get(UserApi::GET_USERID_BY_TOKEN,['token'=>$this->token]);
        return $userId;
    }

    public function setUser($user){
        if(!empty($user)){
            self::$_user = $user;
        }
    }
}