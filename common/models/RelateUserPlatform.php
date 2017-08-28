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

    public static function findLastUpdateTime($userId){
        return self::find()
            ->select('user_id,max(update_time) as update_time')
            ->where(['user_id'=>$userId])
            ->groupBy('user_id')
            ->indexBy('user_id')
            ->asArray(true)
            ->all();
    }

    public static function findListByPlatformPage($platformId,$page,$pagesize){

        $query = self::find()
            ->distinct('user_id')
            ->where(['platform_id'=>$platformId,'status'=>self::STATUS_VALID]);

        $query = $query->offset(($page-1)*$pagesize)->limit($pagesize);

        return $query->asArray(true)->all();
    }


    public static function findCoutByPlatfrom($platformId){
        $query = self::find()
//            ->distinct('user_id')
            ->where(['platform_id'=>$platformId,'status'=>self::STATUS_VALID]);

        $count = $query->count('distinct user_id');

        return intval($count);
    }

    public static function findListByUserPlatformPage($userId,$platformId = -1,$page,$pagesize){
        $query = self::find()
            ->where(['user_id'=>$userId,'status'=>self::STATUS_VALID]);
        $platformId != -1 && $query = $query->andWhere(['platform_id'=>$platformId]);
        $query = $query->offset(($page-1)*$pagesize)->limit($pagesize);
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