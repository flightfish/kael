<?php
namespace common\models;

use Yii;

class RelateDingtalkDepartmentPlatform extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'relate_dingtalk_department_platform';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function findAllList($select='department_id,platform_id'){
        return self::find()
            ->select($select)
            ->where(['status'=>0])
            ->asArray(true)
            ->all();
    }

}
