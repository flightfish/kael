<?php
namespace common\models;

use Yii;

class WorkType extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{work_type}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function findListById($id){
        return self::find()->where(['id'=>$id])->indexBy('id')->asArray(true)->all();
    }

    public static function findAllList(){
        return self::find()->where(['status' => self::STATUS_VALID])->asArray(true)->indexBy('id')->all();
    }

    public static function add($params){
        $model = new self;
        foreach ($params as $k => $v) {
            $model->$k = $v;
        }
        $model->insert();
        return $model->id;
    }
}
