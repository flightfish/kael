<?php
namespace common\models\ehr;

use common\models\DBCommon;
use Yii;

class DepartmentUser extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'department_user';
    }

    public static function getDb()
    {
        return Yii::$app->get('db_ehr');
    }

    public static function add($data)
    {
        $user = new self();
        foreach ($data as $key => $value) {
            $user[$key] = $value;
        }
        $user->insert();
        return $user->id;
    }

    public static function addAllWithColumnRow($columns,$rows,$split=100){
        $rowsList = array_chunk($rows,$split);
        foreach ($rowsList as $rowChunk){
            DBCommon::batchInsertAll(
                static::tableName(),
                $columns,
                $rowChunk,
                static::getDb(),
                'INSERT'
            );
        }
    }

    public static function addUpdateAllWithColumnRow($columns,$rows,$split=100){
        $rowsList = array_chunk($rows,$split);
        foreach ($rowsList as $rowChunk){
            DBCommon::batchInsertAll(
                static::tableName(),
                $columns,
                $rowChunk,
                static::getDb(),
                'UPDATE'
            );
        }
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
