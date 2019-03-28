<?php
namespace common\models;

use Yii;

class DingtalkDepartment extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_department';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findOneByWhere($where,$select='*',$order=""){
        !isset($where['status']) && $where['status'] = 0;
        $query = static::find()
            ->select($select)
            ->where($where);
        !empty($order) && $query = $query->orderBy($order);
        return $query
            ->limit(1)
            ->asArray(true)
            ->one();
    }

}
