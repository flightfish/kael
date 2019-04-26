<?php
namespace common\models\db;


use Yii;

use common\models\BaseActiveRecord;

class EmailRecord extends BaseActiveRecord
{


    public static function tableName()
    {
        return 'email_record';
    }


    public static function getDb()
    {
        return Yii::$app->get('db_ehr');
    }

    public static function add($params){
        $model = new self;
        foreach($params as $k=>$v){
            $model->$k = $v;
        }
        $model->insert();
        return $model->id;
    }

}
