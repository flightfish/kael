<?php

namespace common\models;
use Yii;

class RelateUserPlatform extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{relate_user_platform}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

   public static function findListByUserPlatform($userId,$platformId = -1){
       $query = self::find()
           ->where(['user_id'=>$userId,'status'=>self::STATUS_VALID]);
       $platformId != -1 && $query = $query->andWhere(['platform_id'=>$platformId]);
       return $query->asArray(true)->all();
   }

    public static function findListByPlatform($platformId){
        $query = self::find()
            ->where(['platform_id'=>$platformId]);
        return $query->asArray(true)->all();
    }

    public static function batchAdd($userId,$platfromIdList,$createUserId = 0){
        $columns = ['user_id','platform_id','create_user'];
        $rows = [];
        foreach($platfromIdList as $v){
            $rows[] = [$userId,$v,$createUserId];
        }
        self::batchInsertAll(self::tableName(), $columns, $rows, self::getDb(), 'REPLACE');
    }


}