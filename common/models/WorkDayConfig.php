<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class WorkDayConfig extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'work_day_config';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findDayConfig($dayList){
        return self::findList(['day'=>$dayList]);
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
