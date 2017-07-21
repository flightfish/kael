<?php

namespace common\models;
use Yii;

class RelateDepartmentPlatform extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{relate_department_platform}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

   public static function findListByDepartment($departmentId){
       $query = self::find()
           ->where(['department_id'=>$departmentId,'status'=>self::STATUS_VALID]);
       return $query->asArray(true)->all();
   }

    public static function findListByDepartmentPlatform($departmentId,$platformId){
        $query = self::find()
            ->where(['department_id'=>$departmentId,'platform_id'=>$platformId,'status'=>self::STATUS_VALID]);
        return $query->asArray(true)->all();
    }

}