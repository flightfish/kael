<?php

namespace questionmis\components\cache;


use yii\base\Component;

class BaseCache extends Component
{
    const CACHE_TIME_ONE_DAY = 86400;
    const CACHE_TIME_ONE_HOUR = 3600;
    const CACHE_TIME_TEN_MINUTES = 600;

    const CACHE_PREFIX = "entrystore:";

    public static function gen()
    {
        $args = func_get_args();
        $args[0] = self::CACHE_PREFIX.$args[0];
        return call_user_func_array('sprintf', $args);
    }

    protected static function getRedis()
    {
        return \Yii::$app->redis;
    }

    public static function del($key, $conn = null)
    {
        $conn =  empty($conn) ? self::getRedis() : $conn;

        return $conn->del($key);
    }

    public static function setex($key, $expire, $val, $conn = null)
    {
        $conn =  empty($conn) ? self::getRedis() : $conn;

        if(! is_string($val)) {
            $val= json_encode($val);
        }
        return $conn->setex($key, $expire, $val);
    }

    public static function get($key,$json = false)
    {
        $conn = self::getRedis();
        $data = $conn->get($key);
        return $json ? json_decode($data,true) : $data;
    }

}