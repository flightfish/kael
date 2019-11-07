<?php
namespace common\models;

use Yii;

class DingtalkHrmUserLeave extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_hrm_user_leave';
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

    public static function findOneByUid($userId,$corpType,$select='*',$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        return static::find()
            ->select($select)
            ->where(['user_id'=>$userId,'corp_type'=>$corpType])
            ->asArray(true)
            ->one();
    }

    public static function add($params){
        $model = new self();
        foreach ($params as $k=>$v){
            $model->$k = $v;
        }
        $model->insert();
        return $model->id;
    }
}
