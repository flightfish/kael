<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class CallbackQueue extends \common\models\BaseActiveRecord
{


    public static function tableName()
    {
        return 'callback_queue';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

}
