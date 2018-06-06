<?php
/**
 * Created by IntelliJ IDEA.
 * User: BPCoder
 * Date: 2017/10/26
 * Time: 10:54
 */

namespace common\libs\kmsclient;

class KMSConfig {
    public static $instance = null;
    private $yac = null;
    private $KMSServiceIPListKey = "KMSServiceIPList";
    private $KMSServiceIPListKeyBackup = "KMSServiceIPListBackup";

    private function __construct() {
        $this->yac = new \Yac('KMSConfig');
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getKMSConfig($msServiceName = false) {
        $KMSServiceIPList = $this->yac->get($this->KMSServiceIPListKey);
        \Yii::info('[KMS] [获取配置中心IP列表(Yac)]' . $KMSServiceIPList);
        if ($KMSServiceIPList) {
            $KMSServiceIPList = json_decode($KMSServiceIPList, true);
            if ($msServiceName && isset($KMSServiceIPList[$msServiceName])) {
                return $KMSServiceIPList[$msServiceName];
            }
            return $KMSServiceIPList;
        }
        return $this->getKMSConfigByCurl($msServiceName);
    }

    public function getKMSConfigByCurl($msServiceName) {
        $KMSServiceIPList = [];
        foreach (Config::$configServices as $ip) {
            $url = "http://" . $ip . "/config/lookup";
            $data = HttpCurl::getRequest($url);
            if ($data == false) {
                continue;
            }
            $data = json_decode($data, true);
            if ($data['code'] == "99999" && !empty($data['data'])) {
                $KMSServiceIPList = $data['data'];
                $KMSServiceIPListStr = json_encode($KMSServiceIPList, JSON_UNESCAPED_UNICODE);
                \Yii::info('[KMS] [获取配置中心IP列表(HTTP)]' . $KMSServiceIPListStr);
                $this->yac->set($this->KMSServiceIPListKey, $KMSServiceIPListStr, 300);
                $this->yac->set($this->KMSServiceIPListKeyBackup, $KMSServiceIPListStr);
                break;
            }
        }
        if (empty($KMSServiceIPList)) {
            $KMSServiceIPList = $this->yac->get($this->KMSServiceIPListKeyBackup);
            \Yii::info('[KMS] [获取配置中心IP列表(Yac Backup)]' . $KMSServiceIPList);
            $KMSServiceIPList = json_decode($KMSServiceIPList, true);
        }
        if ($msServiceName && isset($KMSServiceIPList[$msServiceName])) {
            return $KMSServiceIPList[$msServiceName];
        }
        return $KMSServiceIPList;
    }

    public function deleteKMSConfig() {
        $this->yac->delete($this->KMSServiceIPListKey);
    }
}