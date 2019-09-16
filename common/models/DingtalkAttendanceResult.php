<?php
namespace common\models;

use Yii;

class DingtalkAttendanceResult extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_attendance_result';
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
}
