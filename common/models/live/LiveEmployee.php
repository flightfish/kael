<?php
namespace common\models\live;

use usercenter\components\exception\Exception;
use Yii;

class LiveEmployee extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'employee';
    }


    public static function getDb()
    {
        return Yii::$app->get('db_live');
    }





}
