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

    public static function add($data)
    {
        $record = new self();
        foreach ($data as $key => $value) {
            $record[$key] = $value;
        }
        print_r($record);
        exit('#$%^^^^^^');
        $record->insert();
        return $record->id;
    }
}
