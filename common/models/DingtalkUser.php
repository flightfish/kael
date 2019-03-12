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
    
}
