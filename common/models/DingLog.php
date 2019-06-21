<?php
namespace common\models;

use Yii;

class DingLog extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'ding_log';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function add($params)
    {
        $record = new self;
        foreach ($params as $k => $v) {
            $record->$k = $v;
        }
        $record->insert();
        return $record->id;
    }
}
