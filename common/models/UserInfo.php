<?php
namespace common\models;

use Yii;

class UserInfo extends \common\models\BaseActiveRecord
{


    public static function tableName()
    {
        return 'user_info';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }
}
