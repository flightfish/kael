<?php
namespace common\models;

use Yii;

class Department extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{department}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function findListById($departmentId){
        return self::find()->where(['department_id'=>$departmentId])->indexBy('department_id')->asArray(true)->all();
    }

    public static function findAllList(){
        return self::find()->where(['status' => self::STATUS_VALID])->asArray(true)->indexBy('department_id')->all();
    }

    public static function add($params){
        $model = new self;
        foreach ($params as $k => $v) {
            $model->$k = $v;
        }
        $model->insert();
        return $model->department_id;
    }
}
