<?php
namespace common\models;

use Yii;

class Role extends \common\models\BaseActiveRecord
{

    const ROLE_ADMIN = 1;
    const ROLE_DEPARTMENT_ADMIN = 2;

    public static function tableName()
    {
        return '{{role}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function findAllList(){
        return self::find()->where(['status' => self::STATUS_VALID])->asArray(true)->indexBy('role_id')->all();
    }
}
