<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class AlimailStatus extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'alimail_status';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function addEmail($email){
        $model = self::findOne(['email'=>$email]);
        if(empty($model)){
            $model = new self();
        }
        $model->email = $email;
        $model->status = self::STATUS_VALID;
        $model->save();
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
