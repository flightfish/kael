<?php
namespace common\models;

use Yii;

class CommonModulesUser extends \common\models\BaseActiveRecord
{

    public $role_list;
    public $role_group_list;

    public static function tableName()
    {
        return '{{common_modules_user}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findListByModule($module){
        $where = [
                'module'=>$module,
                'status'=>self::STATUS_VALID,
            ];
        return self::find()
            ->where($where)
            ->asArray(true)
            ->all();
    }
}
