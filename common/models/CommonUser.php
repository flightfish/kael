<?php
namespace common\models;

use Yii;

class CommonUser extends \common\models\BaseActiveRecord
{

    public $role_list;
    public $role_group_list;

    public static function tableName()
    {
        return '{{user}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findListById($userIds, $status = self::STATUS_VALID)
    {
        return self::find()
            ->where([
                'status' => $status,
                'id' => $userIds,
            ])
            ->asArray(true)
            ->indexBy('id')
            ->all();
    }

    //增加或更新人员
    public static function saveUser($data)
    {
        if (isset($data['userId']) && !empty($data['userId'])) {
            $user = self::findOne(['id' => $data['userId']]);
        } else {
            $user = new self();
        }
        foreach ($data as $k => $u) {
            if ($k != 'userId') {
                $user[$k] = $u;
            }
        }
        $res = $user->save();
        return $res;
    }

    //增加人员
    public static function addUser($data)
    {
        empty($data['password']) ? $data['password']= md5('123456') : $data['password']=md5($data['password']);
        $user = new self();
        foreach ($data as $key => $value) {
            $user[$key] = $value;
        }
        return $user->insert();
    }

    //编辑人员
    public static function editUser($data)
    {
        return self::updateAll($data, ['id' => $data['id']]);
    }

    //根据电话号码查询未删除人员
    public static function findByMobile($mobile)
    {
        $query = self::find()
            ->where(['mobile' => $mobile, 'status' => 0]);
        return $query->asArray()->one();
    }
    //根据电话号码查询全部人员
    public static function findAllByMobile($mobile)
    {
        $query = self::find()
            ->where(['mobile' => $mobile]);
        return $query->asArray()->one();
    }
    //根据id获取未删除人员
    public static function findByID($userId, $array = false)
    {
        if ($array) {
            return self::find()->where(['id' => $userId, 'status' => 0])->asArray()->one();
        }
        return self::findOne(['id' => $userId, 'status' => 0]);
    }

    //删除人员
    public static function delUser($userId)
    {
        $res1 = self::updateAll(['status' => 1], ['id' => $userId]);
        return $res1;
    }
    //人员列表
    public static function findListByPage($filter=[],$select='*'){
        !isset($filter['status']) && $filter['status'] = self::STATUS_VALID;
        return self::find()
            ->select($select)
            ->where($filter)
            ->asArray(true)
            ->indexBy('id')
            ->all();
    }
    //人员总数
    public static function findCountByFilter($filter=[]){
        !isset($filter['status']) && $filter['status'] = self::STATUS_VALID;
        $count = self::find()
            ->where($filter)
            ->count();
        return intval($count);
    }
    //修改密码
    public static function modifyPassword($userId, $password)
    {
        $res1 = self::updateAll(['password' => $password], ['id' => $userId]);
        return $res1;
    }
}
