<?php
namespace common\models;

use Yii;

class UserCenter extends \common\models\BaseActiveRecord
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

    public static function findOneById($userId, $status = self::STATUS_VALID)
    {
        return self::find()
            ->where(['id' => $userId, 'status' => $status])
            ->asArray(true)
            ->one();
    }



    public static function findUserIdLike($search, $subject = "", $grade = "", $status = self::STATUS_VALID)
    {
        $query = self::find()
            ->select('id')
            ->where(['status' => $status]);
        if (!empty($subject) || $subject!=='') {
            $query = $query->andWhere(['subject' => $subject]);
        }
        if (!empty($grade)) {
            $query = $query->andWhere(['grade_part' => $grade]);
        }
        if (!empty($search)) {
            $query = $query->andWhere(['or', ['like', 'mobile', $search], ['like', 'username', $search], ['id' => $search]]);
        }
        $query = $query->column();
        return $query;
    }


    public static function findUserSearch($page,$pagesize,$search="",$where=[],$leftjoin=[])
    {
        $search = trim(strval($search));
        $query = self::find()
            ->from(self::tableName().' a')
            ->where(['a.status' => self::STATUS_VALID]);
        foreach($leftjoin as $v){
            $query->leftJoin($v[0],$v[1].' and b.status = 0');
        }

        !empty($where) && $query = $query->andWhere($where);

        if (!empty($search)) {
            $query = $query->andWhere(['or', ['like', 'a.mobile', $search], ['like', 'a.username', $search], ['a.id' => $search]]);
        }
        $list = $query->limit($pagesize)->offset(($page - 1) * $pagesize)->groupBy('a.id')->indexBy('id')->asArray(true)->all();
        return $list;
    }

    public static function findUserSearchCount($search="",$where=[],$leftjoin=[])
    {
        $search = trim(strval($search));
        $query = self::find()
            ->select('distinct a.id')
            ->from(self::tableName().' a')
            ->where(['a.status' => self::STATUS_VALID]);
        foreach($leftjoin as $v){
            $query->leftJoin($v[0],$v[1].' and b.status = 0');
        }

        !empty($where) && $query = $query->andWhere($where);

        if (!empty($search)) {
            $query = $query->andWhere(['or', ['like', 'a.mobile', $search], ['like', 'a.username', $search], ['a.id' => $search]]);
        }
        $count = $query->count('distinct a.id');
        return intval($count);
    }

    public static function findAllList($userIds)
    {
        $userList = UserCenter::find()->where(['id' => $userIds])->indexBy('id')->asArray(true)->all();
        foreach ($userList as $k => $v) {
            $v['user_id'] = $v['id'];
            $v['name'] = $v['username'];
            $userList[$k] = $v;
        }
        return $userList;
    }


    public static function findListByDepartment($departmentId,$select='*')
    {
        $userList = UserCenter::find()->select($select)->where(['department_id' => $departmentId])->asArray(true)->all();
        return $userList;
    }

    public static function findListById($userIds, $status = self::STATUS_VALID)
    {
        $where = $status === -1
            ? [
                'id' => $userIds,
            ]
            : [
                'status' => $status,
                'id' => $userIds,
            ];
        return self::find()
            ->where($where)
            ->asArray(true)
            ->indexBy('id')
            ->all();
    }

    public static function addUserInfo($list, $isone = false, $key = 'user_id', $onlyInfo = false)
    {
        if (empty($list)) {
            return [];
        }

        $userIds = $isone ? $list[$key] : array_column($list, $key);
        $list = $isone ? [$list] : $list;

        $userList = self::find()
            ->where([
                'id' => $userIds,
            ])
            ->asArray(true)
            ->indexBy('id')
            ->all();

        foreach ($list as $k => $v) {
            $uid = $v[$key];
            if (!empty($userList[$uid])) {
                $list[$k]['name'] = $userList[$uid]['username'];
                $list[$k]['mobile'] = $userList[$uid]['mobile'];
                $list[$k]['sex'] = $userList[$uid]['sex'];
                if (!$onlyInfo) {
                    $list[$k]['idcard'] = $userList[$uid]['idcard'];
                    $list[$k]['bank_name'] = $userList[$uid]['bank_name'];
                    $list[$k]['bank_deposit'] = $userList[$uid]['bank_deposit'];
                    $list[$k]['bank_area'] = $userList[$uid]['bank_area'];
                    $list[$k]['bank_account'] = $userList[$uid]['bank_account'];
                }
            } else {
                $list[$k]['name'] = "未知用户" . $uid;
                $list[$k]['mobile'] = "";
                $list[$k]['sex'] = 1;
                if (!$onlyInfo) {
                    $list[$k]['idcard'] = "";
                    $list[$k]['bank_name'] = "";
                    $list[$k]['bank_deposit'] = "";
                    $list[$k]['bank_area'] = "";
                    $list[$k]['bank_account'] = "";
                }
            }

        }
        return $isone ? $list[0] : $list;
    }


    public static function findOneWithRoleById($userId)
    {
        $userInfo = self::find()
            ->where([
                'id' => $userId,
                'status' => self::STATUS_VALID,
            ])
            ->asArray(true)
            ->one();
        if (empty($userInfo)) {
            return [];
        }
        $roleList = EntrystoreAuthRelateUserRole::find()->select('role_id')->where(['user_id' => $userId])->column();
        $userInfo['role_list'] = $roleList;
        return $userInfo;
    }

    //增加或更新人员
    public static function saveUser($data)
    {
        if (isset($data['userId']) && !empty($data['userId'])) {
            $user = self::findOne(['user_id' => $data['userId']]);
        } else {
            $user = new self();
        }
        foreach ($data as $k => $u) {
            if ($k != 'userId') {
                $user[$k] = $u;
            }
        }
//        $user->user_id=$data['userId'];
        $res = $user->save();
        return $res;
    }

    //增加人员
    public static function addUser($data)
    {
        $user = new self();
        foreach ($data as $key => $value) {
            $user[$key] = $value;
        }
        $user->insert();
        return $user->id;
    }

    //编辑人员
    public static function editUser($data)
    {
        return self::updateAll($data, ['user_id' => $data['user_id']]);
    }

    //根据电话号码查询未删除人员
    public static function findByMobile($mobile)
    {
        $query = self::find()
            ->where(['mobile' => $mobile, 'status' => 0]);
        return $query->asArray()->one();
    }

    //根据电话号码查询所有人员
    public static function findByMobileStatus($mobile)
    {
        $query = self::find()
            ->where(['mobile' => $mobile, 'status' => 0]);
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

    //根据id获取人员
    public static function findByIDStatus($userId, $array = false)
    {
        if ($array) {
            return self::find()->where(['user_id' => $userId])->asArray()->one();
        }
        return self::findOne(['user_id' => $userId]);
    }

//根据group_id获取人员
    public static function findByGroupID($groupid, $array = false)
    {
        if ($array) {
            return self::find()->where(['group_id' => $groupid, 'status' => 0])->asArray()->one();
        }
        return self::findOne(['user_id' => $groupid, 'status' => 0]);
    }

//删除人员
    public static function delUser($userId)
    {
        $res1 = self::updateAll(['status' => 1], ['user_id' => $userId]);
        return $res1;
    }

    //转移人员
    public static function moveUser($fromId, $toId)
    {
        $res1 = self::updateAll(['group_id' => $toId], ['group_id' => $fromId]);
        return $res1;
    }

    //更改空闲状态
    public static function ChangeUserFree($userId, $freeStatus)
    {
        $res1 = self::updateAll(['free_state' => $freeStatus], ['user_id' => $userId]);
        return $res1;
    }

    //查询总数
    public static function getCount($data)
    {
        $query = self::find()
            ->where($data);
        return $query->count();
    }

    //查询任务总数
    public static function getTaskCount($select, $data)
    {
        $query = self::find()
            ->select($select)
            ->where($data);
        $res = $query->asArray()->column();
        if (!empty($res)) {
            return $res[0];
        }
        return 0;
    }

    public static function findIdListByName($name)
    {
        return self::find()->select('id as user_id')->where(['like', 'username', $name])->column();
    }

    public static function findListByRole($roleId){
        return self::find()->where(['admin'=>$roleId,'status'=>self::STATUS_VALID])->asArray(true)->all();
    }
}
