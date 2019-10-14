<?php
namespace common\models;

use Yii;

class DingtalkRelateDepartmentUser extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_relate_department_user';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

}
