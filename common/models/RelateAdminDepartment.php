<?php

namespace common\models;
use Yii;

class RelateAdminDepartment extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{relate_admin_department}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

   public static function findListByAdmin($userId){
       $query = self::find()
           ->where(['user_id'=>$userId,'status'=>self::STATUS_VALID]);
       return $query->asArray(true)->all();
   }

    public static function findListByAdminDepartment($userId,$department){
        $query = self::find()
            ->where(['user_id'=>$userId,'department_id'=>$department,'status'=>self::STATUS_VALID]);
        return $query->asArray(true)->all();
    }

}