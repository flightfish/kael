<?php
/**
 * Created by PhpStorm.
 * User: 李小雪
 * Date: 2017/1/10
 * Time: 18:05
 */
namespace common\models;
use Yii;

class LogPlatform extends \common\models\BaseActiveRecord
{

    const ADD = 'add';
    const EDIT = 'edit';
    const DEL = 'del';

    public static function tableName()
    {
        return '{{log_auth_platform}}';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function log($op,$platformId,$userId,$data){
        $columns = ['log_op','platform_id','user_id','log_content'];
        $data = is_array($data) ? json_encode($data) : strval($data);
        $rows[] = [
            $op,
            $platformId,
            $userId,
            $data
        ];
        return self::getDb()->createCommand()->batchInsert(self::tableName(),$columns,$rows)->execute();
    }
}