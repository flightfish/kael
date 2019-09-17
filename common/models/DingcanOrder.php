<?php
namespace common\models;

use Yii;

class DingcanOrder extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingcan_order';
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
}
