<?php
namespace common\models;

use Yii;

class DingtalkDepartment extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_department';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


}
