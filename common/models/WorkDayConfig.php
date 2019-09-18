<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class WorkDayConfig extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'work_day_config';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


}
