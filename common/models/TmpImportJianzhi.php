<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class TmpImportJianzhi extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'tmp_import_jianzhi';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

}
