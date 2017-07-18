<?php
namespace usercenter\modules\auth\models;

use Yii;
use linslin\yii2\curl;

class SmsYimei{

    private $urlSendSms = YIMEI_URL_SENDSMS;
    private $urlGetBalance = YIMEI_URL_GETBALANCE;
    private $cdkey = YIMEI_CDKEY;
    private $password = YIMEI_PASSWORD;
    private $addserial = ''; //附加号(最长10位，可置空)
    private $seqid = ''; //长整型值企业内部必须保持唯一，获取状态报告使用
    private $smspriority = '5'; //短信优先级1-5
    //发送消息内容：最长不要超过500个字
    private $message = "";
    private $phone = "";

    private $curlObj = null;

    public function __construct()
    {
        $this->curlObj = new curl\Curl();
        $this->curlObj->setOption(CURLOPT_RETURNTRANSFER, 1);
    }

    public function getBalance($params)
    {
        $params = [
            'cdkey' => isset($params['cdkey']) ?
                $params['cdkey'] : $this->cdkey,
            'password'=> isset($params['password']) ?
                $params['password'] : $this->password,
        ];

        $url = $this->urlGetBalance . '?' .http_build_query($params);

        $response = $this->curlObj->get($url);

        $ret = preg_match('#<response><error>0</error><message>(.*)</message></response>#', $response, $match);

        if($ret>0)
            return $match[1];

        return false;
    }

    /**
     * @brief 发短信
     * @param $array 调用参数
     * @returns boolean,此处返回值只表示发送是否成功，不表示短信是否成功被接收
     */
    public function sendSMS($params)
    {
        $params = [
            'cdkey' => isset($params['cdkey']) ?
                $params['cdkey'] : $this->cdkey,
            'password'=> isset($params['password']) ?
                $params['password'] : $this->password,
            'addserial'=> isset($params['addserial']) ?
                $params['addserial'] : $this->addserial,
            'seqid'=> isset($params['seqid']) ?
                $params['seqid'] : $this->seqid,
            'smspriority'=> isset($params['smspriority']) ?
                $params['smspriority'] : $this->smspriority,
            'phone' => implode(',', $params['phones']),
            'message' => $this->formatContent($params['content']),
        ];
        $url = $this->urlSendSms . '?' .http_build_query($params);
        $response = $this->curlObj->get($url);
        $ret = preg_match('#<response><error>([\w]+)</error>#', $response, $match);

        if($ret>0 && $match[1] == '0')
            return true;

        $log = '[SMS_ALARM_YIMEI],url[' .$url. ']';
        Yii::warning($log, 'knowbox');
        return false;

    }

    /*
     * 短信内容(最多500个汉字)（utf8）编码
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

//$params = [
    //'phones' => ['18811006702'],
    //'content' => '您的注册验证码：1233【作业盒子】',
//];
//$smsService = new SmsYimei;
//$ret = $smsService->sendSMS($params);
//var_dump($ret);exit;
