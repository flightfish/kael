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


    public static function findOneByHost($host,$platformId){
        if(empty($host)){
            return null;
        }
        if(empty($platformId)){
            return self::find()->where(['status'=>self::STATUS_VALID])->andWhere(['like','platform_url','//'.$host])->asArray(true)->one();
        }else{
            return self::find()->where(['status'=>self::STATUS_VALID,'platform_id'=>$platformId])->andWhere(['like','platform_api',$host])->asArray(true)->one();
        }
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



    public static function findPageList($page,$pagesize,$where=[],$order="",$select='*',$extWhere=[],$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        $query = static::find()
            ->select($select)
            ->where($where);
        foreach ($extWhere as $v){
            $query = $query->andWhere($v);
        }
        $query = $query
            ->limit($pagesize)
            ->offset(($page-1)*$pagesize);
        !empty($order) && $query->orderBy($order);
        return $query->asArray(true)->all();
    }

    public static function findCount($where=[],$extWhere=[],$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = 0;
        $query = static::find()
            ->where($where);
        foreach ($extWhere as $v){
            $query = $query->andWhere($v);
        }
        return $query->count();
    }


    public static function add($params){
        $model = new self();
        foreach ($params as $k=>$v){
            $model->$k = $v;
        }
        $model->insert();
        return $model->platform_id;
    }
}
