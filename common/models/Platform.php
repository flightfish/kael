<?php
namespace common\models;

use Yii;

class Platform extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return '{{platform}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function findOneByHost($host){
        if(empty($host)){
            return null;
        }
        return self::find()->where(['status'=>self::STATUS_VALID])->andWhere(['like','platform_url',$host])->asArray(true)->one();
    }

    public static function findListById($platformId){
        if(empty($platformId)){
            return [];
        }
        $query =  self::find()->where(['status'=>self::STATUS_VALID,'platform_id'=>$platformId]);
        return $query->indexBy('platform_id')->asArray(true)->all();
    }
    public static function findAllList(){
        return self::find()->where(['status' => self::STATUS_VALID])->asArray(true)->indexBy('platform_id')->all();
    }

    public static function findOneById($platformId){
        return self::find()->where(['status'=>self::STATUS_VALID,'platform_id'=>$platformId])
            ->asArray(true)->one();
    }
}
