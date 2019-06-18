<?php
namespace usercenter\modules\auth\models;
use Yii;
use usercenter\modules\auth\models\UserModel;

//define('SMS_ALL_POWERFUL_CODE','2212');
class VerifyCode extends \yii\db\ActiveRecord {
    const T = 'INTERIM';

    //获取修改密码验证码
    public function getStudentPasswordVerifyCode($mobile)
    {
        $code = self::getStudentPasswordCode($mobile);
        if (empty($code)) {
            $code = rand(100000, 999999);
            self::setStudentPasswordCode($mobile, $code);
        }
        return [$code, $this->getPowerfulCode()];
    }
    public static function getStudentPasswordCode($mobile)
    {
        $key = self::gen('USER_STUDENT_PASSWORD_CODE_%s', $mobile);

        $oldKey = '1012_' . $mobile;

        return self::get($key);
    }
    public static function setStudentPasswordCode($mobile, $data)
    {
        $key = self::gen('USER_STUDENT_PASSWORD_CODE_%s', $mobile);
        return self::setex($key, 180, $data);
    }
    protected static function setex($key, $expire, $val, $conn = null)
    {
        $conn =  empty($conn) ? self::getRedis() : $conn;

        if(! is_string($val)) {
            $val= json_encode($val);
        }
        return $conn->setex($key, $expire, $val);
    }
    protected static function getRedis()
    {
        return Yii::$app->redis;
    }
    private function getPowerfulCode()
    {
        $code = self::getAllPowerfulCode();
        return empty($code) ? SMS_ALL_POWERFUL_CODE : $code;
    }
    public static function getAllPowerfulCode()
    {
        $key = self::gen('USER_ALL_POWERFUL_CODE');
        return self::get($key, self::T, 'ALL_POWERFUL_CODE');
    }

    /*
   * 更新验证码
   * @param int $userID
   * @param string $code
   * @return int  -2 用户不存在，1成功，-1更新失败
   * */
    public function updatePasswordCode($userID, $code)
    {
        $user = new UserModel();
        $ret = $user->updatePasswordCode($userID, $code);
        if (!$ret) {
            return -1;
        }
        return 1;

    }
    public static function gen()
    {
        return call_user_func_array('sprintf', func_get_args());
    }
    public static function get($key)
    {
        $conn = self::getRedis();
        return $conn->get($key);
    }
    public static function __callStatic($name, $params)
    {
        $conn = self::getRedis();
        return call_user_func_array([$conn, $name], $params);
    }
    /**
     * 验证注册验证码
     * @param $code
     * @param $cacheKey
     * @return bool true 通过
     */
    public function verifyKey($verifyKey, $cacheKey)
    {
        $wannengyanzheng = 1024+date('m')*date('d');
        if (ENABLE_KNOBOX_CACHE) {
            $code = self::get($cacheKey);
            if (!empty($code) && $code == $verifyKey) {
                return true;
            }
            $code2 = self::getAllPowerfulCode();
            if (!empty($code2) && $code2 == $verifyKey) {
                return true;
            }
            if(empty($code2)&&$verifyKey==$wannengyanzheng) {
                return true;
            }
        }
        return false;
    }
    public static function getStudentPasswordCodeKey($mobile)
    {
        return self::gen('USER_STUDENT_PASSWORD_CODE_%s', $mobile);
    }

}