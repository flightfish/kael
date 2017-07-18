<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2017/1/10
 * Time: 18:05
 */
namespace common\models;
use Yii;

class LogAuthUser extends \common\models\BaseActiveRecord
{
    const OP_ADD_GROUP = 1;//添加组
    const OP_DEL_GROUP = 2;//删除组
    const OP_EDIT_GROUP = 3;//编辑组
    const OP_ADD_USER = 4;//添加人员
    const OP_DEL_USER = 5;//删除人员
    const OP_EDIT_USER = 6;//编辑人员
    const OP_ADD_USER_ROLE = 7;//增加人员角色
    const OP_EDIT_USER_ROLE = 8;//更改人员角色
    const OP_DEL_USER_ROLE = 9;//删除人员角色
    const OP_CHANGE_GROUP = 13;//转移组
    const OP_LOGIN = 14;//登录
    const OP_LOGIN_OUT = 20;//登出
    const OP_CHANGE_PASS = 15;//改密码
    const OP_FOGET_PASS = 16;//忘记密码
    const OP_SEND_CODE = 17;//发送验证码
    const OP_VERIFY_CODE = 18;//验证验证码
    const OP_CHANGE_FREE = 19;//更改空闲状态
    public static function tableName()
    {
        return '{{log_auth_user}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function LogGroup($adminId,$groupIds,$op,$data){
        $columns = ['admin_id','group_id','op_type','memo'];
        $data = is_array($data) ? json_encode($data) : strval($data);
        !is_array($groupIds) && $groupIds =  [$groupIds];
        foreach($groupIds as $groupId){
            $rows[] = [
                $adminId,
                $groupId,
                $op,
                $data
            ];
        }
        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
    public static function LogUser($adminId,$userIds,$op,$data){
        $columns = ['admin_id','user_id','op_type','memo'];
        $data = is_array($data) ? json_encode($data) : strval($data);
        !is_array($userIds) && $userIds =  [$userIds];
        foreach($userIds as $userId){
            $rows[] = [
                $adminId,
                $userId,
                $op,
                $data
            ];
        }
        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
    public static function LogUserRole($adminId,$userIds,$roleId,$op,$data){
        $columns = ['admin_id','user_id','role_id','op_type','memo'];
        $roleId = is_array($roleId) ? json_encode($roleId) : strval($roleId);
        $data = is_array($data) ? json_encode($data) : strval($data);
        !is_array($userIds) && $userIds =  [$userIds];
        foreach($userIds as $userId){
            $rows[] = [
                $adminId,
                $userId,
                $roleId,
                $op,
                $data
            ];
        }
        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
    public static function LogUserFree($adminId,$freeIds,$op,$data){
        $columns = ['admin_id','free_status','op_type','memo'];
        $data = is_array($data) ? json_encode($data) : strval($data);
        !is_array($freeIds) && $freeIds =  [$freeIds];
        foreach($freeIds as $freeId){
            $rows[] = [
                $adminId,
                $freeId,
                $op,
                $data
            ];
        }

        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
    public static function LogLogin($adminId,$op,$data){
        $columns = ['admin_id','op_type','memo'];
        $data = is_array($data) ? json_encode($data) : strval($data);
        $rows[] = [
            $adminId,
            $op,
            $data
        ];
        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
}