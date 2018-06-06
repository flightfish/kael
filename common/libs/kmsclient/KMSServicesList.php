<?php
/**
 * Created by IntelliJ IDEA.
 * User: BPCoder
 * Date: 2017/10/26
 * Time: 12:21
 */

namespace common\libs\kmsclient;

class KMSServicesList {
    public static $sms = [
//        "sendSMS" => [
//            "uri" => "/sms/send-sms",
//            "timeout" => 100,
//        ],
//        "sendMarketingSMS" => [
//            "uri" => "/sms/send-marketing-sms",
//            "timeout" => 150,
//        ],
        "sendNoticeSMS" => [
            "uri" => "/sms/send-notice-sms",
            "timeout" => 500,
        ],
    ];
}