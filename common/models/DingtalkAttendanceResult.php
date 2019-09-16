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


}
