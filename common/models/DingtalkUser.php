<?php
namespace common\models;

use Yii;

class DingtalkUser extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'dingtalk_user';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function add($params){
        $model = new self();
        foreach ($params as $k=>$v){
            $model->$k = $v;
        }
        $model->insert();
        return $model->auto_id;
    }

    public static function findOneByWhere($where,$select='*',$order="",$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = 0;
        $query = static::find()
            ->select($select)
            ->where($where);
        !empty($order) && $query = $query->orderBy($order);
        return $query
            ->limit(1)
            ->asArray(true)
            ->one();
    }

    public static function findList($where=[],$indexKey="",$select='*',$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        if(!empty($indexKey)){
            return static::find()
                ->select($select)
                ->where($where)
                ->indexBy($indexKey)
                ->asArray(true)
                ->all();
        }
        return static::find()
            ->select($select)
            ->where($where)
            ->asArray(true)
            ->all();
    }

    public static function findListByWhereWithWhereArr($where,$whereArr,$select='*',$order='',$limit=0,$status=0){
        !isset($where['status']) && $status != -1 && $where['status'] = $status;
        $query =  self::find()->select($select)->where($where);
        foreach ($whereArr as $v){
            $query = $query->andWhere($v);
        }
        !empty($order) && $query = $query->orderBy($order);
        $limit>0 && $query = $query->limit($limit);
        return $query->asArray(true)->all();
    }
}
