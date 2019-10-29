<?php
namespace common\models;

use usercenter\components\exception\Exception;
use Yii;

class CallbackRegister extends \common\models\BaseActiveRecord
{

    const NOTICE_TYPE_ADDPRIV = 1;
    const NOTICE_TYPE_DELPRIV = 2;

    public static function tableName()
    {
        return 'callback_register';
    }


    public static function getDb()
    {
        return Yii::$app->get('db');
    }


    public static function findAllList($noticeType){
        return self::find()->where(['status'=>0,'notice_type'=>$noticeType])->asArray(true)->all();
    }
}
