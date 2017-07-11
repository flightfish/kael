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
}
