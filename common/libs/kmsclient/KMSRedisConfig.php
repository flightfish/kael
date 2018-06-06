<?php
/**
 * Created by IntelliJ IDEA.
 * User: BPCoder
 * Date: 2017/11/21
 * Time: 11:27
 */

namespace common\libs\kmsclient;

class KMSRedisConfig {
    public static $instance = null;
    private $yac = null;
    private $KMSRedisConfigKey = "KMSRedisConfig";
    private $KMSRedisConfigKeyBackup = "KMSRedisConfigBackup";

    private function __construct() {
        $this->yac = new \Yac('KMSConfig');
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getKMSConfig($nodeName = false) {
        $KMSRedisConfig = $this->yac->get($this->KMSRedisConfigKey);
        \Yii::info('[KMS] [获取配置中心Redis列表(Yac)]' . $KMSRedisConfig);
        if ($KMSRedisConfig) {
            $KMSRedisConfig = json_decode($KMSRedisConfig, true);
            if ($nodeName && isset($KMSRedisConfig[$nodeName])) {
                return $KMSRedisConfig[$nodeName];
            }
            return $KMSRedisConfig;
        }
        return $this->getKMSConfigByCurl($nodeName);
    }

    public function getKMSConfigByCurl($nodeName) {
        $KMSRedisConfig = [];
        foreach (Config::$configServices as $ip) {
            $url = "http://" . $ip . "/config/redis";
            $data = HttpCurl::getRequest($url);
            if ($data == false) {
                continue;
            }
            $data = json_decode($data, true);
            if ($data['code'] == "99999" && !empty($data['data'])) {
                $KMSRedisConfig = $data['data'];
                $KMSRedisConfigStr = json_encode($KMSRedisConfig, JSON_UNESCAPED_UNICODE);
                \Yii::info('[KMS] [获取配置中心Redis列表(HTTP)]' . $KMSRedisConfigStr);
                $this->yac->set($this->KMSRedisConfigKey, $KMSRedisConfigStr, 300);
                $this->yac->set($this->KMSRedisConfigKeyBackup, $KMSRedisConfigStr);
                break;
            }
        }
        if (empty($KMSRedisConfig)) {
            $KMSRedisConfig = $this->yac->get($this->KMSRedisConfigKeyBackup);
            \Yii::info('[KMS] [获取配置中心Redis列表(Yac Backup)]' . $KMSRedisConfig);
            $KMSRedisConfig = json_decode($KMSRedisConfig, true);
        }
        if ($nodeName && isset($KMSRedisConfig[$nodeName])) {
            return $KMSRedisConfig[$nodeName];
        }
        return $KMSRedisConfig;
    }
}