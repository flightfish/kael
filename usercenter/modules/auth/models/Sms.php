<?php

namespace usercenter\modules\auth\models;

use usercenter\lib\Log;
use Yii;
use yii\base\Exception;

class Sms{


    /**
     * 发送短信
     * @param $mobile
     * @param $content  发送的内容
     * @param int $type  短信类型  0极致 1皮卡 2国都 3亿美
     * @return bool  成功or失败
     */
    public function SendSMS($mobile,$content,$type=1)
    {
        //切换默认通道为亿美
        if($type == 1){
            $type = 3;
        }

        //添加签名
        if($type == 3)
        {
            if(strpos($content, '【作业盒子】') === false)
                $content = '【作业盒子】' . $content;
        }

        $params = [
            'phones' => [$mobile],
            'content' => $content,
        ];
        $ret=false;
//        var_dump($type);die();
        try{
            if ($type == 1) {
                $sms = new SmsPica();
                $ret = $sms->sendSMS($params);
            //} else if($type == 0){
                //$sms = new SmsHdw();
                //$ret = $sms->sendSMS($params);
            } else if($type == 2 || $type == 0){
                $sms = new SmsGuodu();
                $ret = $sms->sendSMS($params);
            } else if($type == 3){
                $sms = new SmsYimei();
                $ret = $sms->sendSMS($params);
            }
        }
        catch(Exception $e)
        {
            Log::LogError($e->getMessage() . $e->getLine(), 'sms');
        }

        return $ret;
    }


}
