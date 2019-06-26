<?php
namespace common\models\ehr;

use Yii;

class ConcernAnniversaryRecord extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'concern_anniversary_record';
    }

    public static function getDb()
    {
        return Yii::$app->get('db_ehr');
    }

    public static function findOneByWhere($where,$select='*',$order="",$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = 0;
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
