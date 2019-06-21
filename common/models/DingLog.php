<?php
namespace common\models;

use Yii;

class DingLog extends \common\models\BaseActiveRecord
{

    public $role_list;
    public $role_group_list;

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
        $user = new self();
        foreach ($data as $key => $value) {
            $user[$key] = $value;
        }
        $user->insert();
        return $user->id;
    }
}
