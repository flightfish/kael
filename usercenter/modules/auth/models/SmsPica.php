<?php
namespace usercenter\modules\auth\models;

use Yii;
use linslin\yii2\curl;

class SmsPica{

    private $urlSendSms = PICA_URL_SENDSMS;
    private $urlGetBalance = PICA_URL_GETBALANCE;
    private $regcode = PICA_REGCODE;
    private $passwd = PICA_PASSWD;
    private $hardKey = PICA_HARDKEY;

    private $curlObj = null;

    public function __construct()
    {
        $this->curlObj = new curl\Curl();
        $this->curlObj->setOption(CURLOPT_RETURNTRANSFER, 1);
    }

    public function getBalance($params)
    {
        $params = [
            'regcode' => isset($params['regcode']) ?
                $params['regcode'] : $this->regcode,
            'pwd'=> isset($params['passwd']) ?
                $params['passwd'] : $this->passwd,
            'hardkey' => isset($params['hardKey']) ?
                $params['hardKey'] : $this->hardKey,
        ];

        $url = $this->urlGetBalance . '?' .http_build_query($params);

        $response = $this->curlObj->get($url);

        $ret = preg_match('#<response><result>([\w]+)</result></response>#', $response, $match);

        if($ret>0 && $match[1]==0)
            return true;

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
            'regcode' => isset($params['regcode']) ?
                $params['regcode'] : $this->regcode,
            'pwd'=> isset($params['passwd']) ?
                $params['passwd'] : $this->passwd,
            'phone' => implode(',', $params['phones']),
            'content' => $this->formatContent($params['content']),
            'extnum' => isset($params['extnum']) ?
                $params['extnum'] : '',
            'level' => isset($params['level']) ? : 1,
            'schtime' => isset($params['schtime']) ?
                $params['schtime'] : '',
            'reportflag' => isset($params['reportflag']) ?
                $params['reportflag'] : 1,
            'url' => isset($params['url']) ?
                $params['url'] : '',
            'smstype' => isset($params['smstype']) ?
                $params['smstype'] : 4,
            'hardkey' => isset($params['hardKey']) ?
                $params['hardKey'] : $this->hardKey,
        ];
        $url = $this->urlSendSms . '?' .http_build_query($params);
        $response = $this->curlObj->get($url);

        $ret = preg_match('#<response><result>([\w]+)</result></response>#', $response, $match);

        if($ret>0 && $match[1]==0)
            return true;

        $log = '[SMS_ALARM_PICA],url[' .$url. ']';
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
//$smsService = new SmsPica;
//$ret = $smsService->sendSMS($params);
//var_dump($ret);exit;
