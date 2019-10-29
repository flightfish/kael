<?php
namespace common\models;

use Yii;

class DingcanOrderException extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingcan_order_exception';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function addUpdateColumnRows($columns,$rows){
        if(empty($rows)){
            return false;
        }
        $rowsChunkList = array_chunk($rows,300);
        foreach ($rowsChunkList as $rowsChunkOne){
            DBCommon::batchInsertAll(self::tableName(),$columns,$rowsChunkOne,self::getDb(),'UPDATE');
        }
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
    public static function findListByWhereWithWhereArr($where,$whereArr,$select='*',$order='',$limit=0,$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        $query =  self::find()->select($select)->where($where);
        foreach ($whereArr as $v){
            $query = $query->andWhere($v);
        }
        !empty($order) && $query = $query->orderBy($order);
        $limit>0 && $query = $query->limit($limit);
        return $query->asArray(true)->all();
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
