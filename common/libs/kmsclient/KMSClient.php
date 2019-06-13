<?php
/**
 * Created by IntelliJ IDEA.
 * User: BPCoder
 * Date: 2017/10/26
 * Time: 10:52
 */

namespace common\libs\kmsclient;

class KMSClient {
    public static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getResult($msServiceName, $uriName, $data, $hashKey = false, $isLast = false) {
        $KMSServiceIPList = KMSConfig::getInstance()->getKMSConfig($msServiceName);
        if (empty($KMSServiceIPList) && isset(KMSServicesList::${$msServiceName}[$uriName])) {
            return [false, [1]];
        }
        $KMSService = KMSServicesList::${$msServiceName}[$uriName];
        if ($hashKey != false && $isLast == false) {
            if (is_numeric($hashKey)) {
                $ip = $KMSServiceIPList[$hashKey % count($KMSServiceIPList)];
            } else {
                $ip = $KMSServiceIPList[crc32($hashKey) % count($KMSServiceIPList)];
            }
        } else {
            $ip = $KMSServiceIPList[array_rand($KMSServiceIPList)];
        }
        $url = "http://" . $ip . $KMSService['uri'];
        $data['kmsSource'] = Config::$kmsSource;
        $result = HttpCurl::postRequest($url, $data, $KMSService['timeout']);
        \Yii::info('[KMS] [获取微服务] ' . json_encode([$url, $data, $KMSService['timeout'], $result]));
        if ($result == false) {
            if ($isLast == false) {
                KMSConfig::getInstance()->deleteKMSConfig();
                return $this->getResult($msServiceName, $uriName, $data, $hashKey, true);
            }
            return [false, [2,$url]];
        }
        $result = json_decode($result, true);
        return [true, $result];
    }
}