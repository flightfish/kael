<?php
/**
 * Created by PhpStorm.
 * User: lixx
 */

namespace common\libs;


use Yii;

class Cache
{
    public static function checkCache($cacheKey)
    {
        $cacheKey = $cacheKey[0] . '_' . md5(join($cacheKey, '-'));
        if (isset($_GET['unCache']) && $_GET['unCache'] == 1) {
            return null;
        }
        if (Yii::$app->params['redis_cache']) {
            $ret = Yii::$app->redis->get($cacheKey);
            if ($ret !== null) {
                $ret = json_decode($ret, true);
                return $ret;
            }
        }
        return null;
    }

    public static function setCache($cacheKey, $ret)
    {
        $cacheKey = $cacheKey[0] . '_' . md5(join($cacheKey, '-'));
        if (Yii::$app->params['redis_cache'] and !empty($ret)) {
            Yii::$app->redis->setex($cacheKey, Yii::$app->params['redis_cache_time'], json_encode($ret));
        }
    }

}