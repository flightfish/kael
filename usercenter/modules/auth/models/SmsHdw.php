<?php
namespace usercenter\modules\auth\models;

use Yii;
use linslin\yii2\curl;

class SmsHdw
{
    private $urlSendSms = HDW_URL_SENDSMS_URL;
    private $cdkey = HDW_CDKEY;
    private $passwd = HDW_PASSWD;

    private $curlObj = null;

    public function __construct()
    {
        $this->curlObj = new curl\Curl();
        $this->curlObj->setOption(CURLOPT_RETURNTRANSFER, 1);
    }

    /**
     * @brief 发短信
     * @param $array 调用参数
     * @returns boolean,此处返回值只表示发送是否成功，不表示短信是否成功被接收
     */
    public function sendSMS($params)
    {

        //http://ws.hdwinfo.cn:8080/sdkproxy/sendsms.action?cdkey=JZK-6364DJBQOM&password=232713267&phone=18600678273,13811209065&message=%E3%80%90%E7%9F%A5%E8%AF%86%E5%8D%B0%E8%B1%A1%E3%80%91%E6%82%A8%E7%9A%84%E9%AA%8C%E8%AF%81%E7%A0%81%E6%98%AF%EF%BC%9A1233&addserial=&smspriority=5
        $params = [
            'cdkey' => $this->cdkey,
            'password'=> $this->passwd,
            'phone' => implode(',', $params['phones']),
            'message' => $this->formatContent($params['content']),
            'addserial' =>  '',
            'smspriority' => isset($params['smspriority']) ? $params['smspriority']: 5,

        ];
        $url = $this->urlSendSms . '?' .http_build_query($params);
        $response = $this->curlObj->get($url);
        $ret = preg_match('/0/', $response, $match);
        if($ret>0 && $match[0]==0)
            return true;

        $log = '[SMS_ALARM_HDW],url[' .$url. ']';
        Yii::warning($log, 'knowbox');
        return false;

    }

    /*
     * 短信内容(最多70个字)（gbk）编码
     */
    private function formatContent($content)
    {
        $encode = mb_detect_encoding($content,
            array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')
        );

        //$strEncode = iconv($encode,"gb2312//IGNORE",$content);
        $strEncode = mb_convert_encoding($content, 'UTF-8', $encode);
        return $strEncode;
    }
}
