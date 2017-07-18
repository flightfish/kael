<?php
namespace usercenter\modules\auth\models;

use Yii;
use linslin\yii2\curl;

class SmsGuodu{

    private $urlSendSms = GUODU_URL_SENDSMS;
    private $urlGetBalance = GUODU_URL_GETBALANCE;
    private $operID = GUODU_OPERID;
    private $operPass = GUODU_OPERPASS;
    //发送时间：YYYYMMDDHHMMSS格式,为空表示立即发送
    private $sendTime = '';
    //消息有效期：YYYYMMDDHHMMSS格式,规定时间内发送失败则重发,过时作废
    private $validTime = '';
    private $appendID = '';
    //消息类型：取值有15和8。15以普通短信形式下发,8以长短信形式下发
    private $contentType = '15';
    //发送消息内容：最长不要超过500个字
    private $content = "";

    private $curlObj = null;

    public function __construct()
    {
        $this->curlObj = new curl\Curl();
        $this->curlObj->setOption(CURLOPT_RETURNTRANSFER, 1);
    }

    public function setOperID($operID)
    {
        $this->operID = $operID;
    }

    public function setOperPass($operPass)
    {
        $this->operPass = $operPass;
    }

    public function getBalance($params)
    {
        $params = [
            'OperID' => isset($params['operID']) ?
                $params['operID'] : $this->operID,
            'OperPass'=> isset($params['operPass']) ?
                $params['operPass'] : $this->operPass,
        ];

        $url = $this->urlGetBalance . '?' .http_build_query($params);

        $response = $this->curlObj->get($url);

        $ret = preg_match('#([\w]+)#', $response, $match);

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
            'OperID' => isset($params['operID']) ?
                $params['operID'] : $this->operID,
            'OperPass'=> isset($params['operPass']) ?
                $params['operPass'] : $this->operPass,
            'SendTime' => isset($params['sendTime']) ?
                $params['sendTime'] : $this->sendTime,
            'ValidTime'=> isset($params['validTime']) ?
                $params['validTime'] : $this->validTime,
            'AppendID'=> isset($params['appendID']) ?
                $params['appendID'] : $this->appendID,
            'DesMobile' => implode(',', $params['phones']),
            'Content' => $this->formatContent($params['content']),
            'ContentType' => isset($params['contentType']) ?
                $params['contentType'] : $this->contentType,
        ];
        $url = $this->urlSendSms . '?' .http_build_query($params);
        $response = $this->curlObj->get($url);
        $ret = preg_match('#<response><code>([\w]+)</code>#', $response, $match);

        if($ret>0 && $match[1] == '03')
            return true;

        $log = '[SMS_ALARM_GUODU],url[' .$url. ']';
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
        $strEncode = mb_convert_encoding($content, 'GBK', $encode);
        return $strEncode;
    }

}

//$params = [
    //'phones' => ['18811006702'],
    //'content' => '您的注册验证码：1233【作业盒子】',
//];
//$smsService = new SmsGuodu;
//$ret = $smsService->sendSMS($params);
//var_dump($ret);exit;
