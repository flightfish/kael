<?php
namespace common\libs;


class ElasticsearchBase{

    public static $indexPrefix = "product_";
    public static $type;
    public static $host;

    public static $analyzer = "ik_max_word";
    public static $analyzerSearch = "ik_max_word";//"ik_smart";

    public static function find(){
        $host  = \Yii::$app->params['elasticsearch_url'];
        $index = self::$indexPrefix . \Yii::$app->params['product_id'];
        return new Elasticsearch($host,$index,static::$type);
    }
}
