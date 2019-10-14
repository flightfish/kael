<?php
namespace common\models;

use Yii;

class DingtalkDepartmentUser extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_department_user';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findList($where=[],$indexKey="",$select='*',$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        if(!empty($indexKey)){
            return static::find()
                ->select($select)
                ->where($where)
                ->indexBy($indexKey)
                ->asArray(true)
                ->all();
        }
        return static::find()
            ->select($select)
            ->where($where)
            ->asArray(true)
            ->all();
    }
}
