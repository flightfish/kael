<?php
namespace common\models\ehr;

use Yii;

class TrCandidateForSchool extends \common\models\BaseActiveRecord
{

    public static function tableName()
    {
        return 'tr_candidate_for_school';
    }

    public static function getDb()
    {
        return Yii::$app->get('db_ehr');
    }

    public static function findList($where,$select='*'){
        return self::find()->select($select)->where($where)->andWhere(['status'=>0])->asArray(true)->all();
    }

}
