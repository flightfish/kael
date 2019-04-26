<?php
namespace common\models\redis;

use common\components\cache\BaseCache;

class DingCache extends BaseCache
{

    const KEY_DING = "KEY_DING_%s";

    public static function setDing($param,$data,$expire){
        $key = self::gen(self::KEY_DING,$param);
        !$expire  && $expire = 60*60*24;
        return self::setex($key,$expire,$data);
    }
    public static function getDing($param){
        $key = self::gen(self::KEY_DING,$param);
        return self::get($key,false);
    }
}
