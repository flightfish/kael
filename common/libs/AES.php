<?php

namespace common\libs;
use Yii;

class AES{
     
    private $_secret_key = 'yestem11yestem11yestem11yestem11';
     
    public function setKey($key) {
        $this->_secret_key = $key;
    }
    const CIPHER = MCRYPT_RIJNDAEL_128;
    const MODE = MCRYPT_MODE_ECB;

    function addPkcs7Padding($string, $blocksize = 32) {
        $len = strlen($string); //取得字符串长度
        $pad = $blocksize - ($len % $blocksize); //取得补码的长度
        $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
        return $string;
    }

    function stripPkcs7Padding($string){
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if(preg_match("/$slastc{".$slast."}/", $string)){
            $string = substr($string, 0, strlen($string)-$slast);
            return $string;
        } else {
            return false;
        }
    }


    public function encode($data) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER, self::MODE),MCRYPT_RAND);
        $padding = $this->addPkcs7Padding($data);
        return base64_encode(mcrypt_encrypt(self::CIPHER, $this->_secret_key, $this->addPkcs7Padding($data, 16), self::MODE, $iv));
        return base64_encode(mcrypt_encrypt(self::CIPHER, $this->_secret_key, $data, self::MODE, $iv));
    }
     
    public function decode($data) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(self::CIPHER,self::MODE),MCRYPT_RAND);
        $encryptedText =base64_decode($data);
        return $this->stripPkcs7Padding(mcrypt_decrypt(self::CIPHER, $this->_secret_key, $encryptedText, self::MODE, $iv));
    }
}

//FOR TEST
#$aes = new aes();

// 加密
#$string = $aes->encode('18888888884||f379eaf3c831b04de153469d1bec345e');
#$string = $aes->encode('13936272833||b2f2f1ee4424a325c49aea6500fe5ca0');
#echo $string."\n";
// 解密
#$decodestring = $aes->decode($string);
#$decodestring = $aes->decode("X7zQ4rewQyY52NoC2ttsvJKhdT+pjwUkGg0lqETNH9kMdXZKLLtjoy/aFEYjFawC");
#$decodestring = $aes->decode("M5ewAyKTTaBXNxfDAaenJdrMHUobrkUhoQANsUzXDA4QLdxpJFKYk6tdwzzcYKmA");
#echo $decodestring;
?>
