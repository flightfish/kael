<?php
namespace common\models;

use Yii;

class DingtalkAttendanceProcessInstance extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_attendance_process_instance';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function add($params){
        $model = new self();
        foreach ($params as $k=>$v){
            $model->$k = $v;
        }
        return $model->insert();
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
}
