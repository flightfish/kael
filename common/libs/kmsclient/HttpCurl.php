<?php
/**
 * Created by IntelliJ IDEA.
 * User: BPCoder
 * Date: 2017/10/26
 * Time: 10:28
 */

namespace common\libs\kmsclient;

class HttpCurl {

    public static $postMode = 'POST';

    public static $getMode = 'GET';

    /**
     * 模拟POST与GET请求
     *
     * Examples:
     * ```
     * HttpCurl::request('http://example.com/', 'post', array(
     *  'user_uid' => 'root',
     *  'user_pwd' => '123456'
     * ));
     *
     * HttpCurl::request('http://example.com/', 'post', '{"name": "peter"}');
     *
     * HttpCurl::request('http://example.com/', 'post', array(
     *  'file1' => '@/data/sky.jpg',
     *  'file2' => '@/data/bird.jpg'
     * ));
     *
     * HttpCurl::request('http://example.com/', 'get');
     *
     * HttpCurl::request('http://example.com/?a=123', 'get', array('b'=>456));
     * ```
     *
     * @param string $url [请求地址]
     * @param string $type [请求方式 post or get]
     * @param bool|string|array $data [传递的参数]
     * @param array $header [可选：请求头] eg: ['Content-Type:application/json']
     * @param int $timeout [可选：超时时间毫秒]
     *
     * @return array($body, $header, $status, $errno, $error)
     * - $body string [响应正文]
     * - $header string [响应头]
     * - $status array [响应状态]
     * - $errno int [错误码]
     * - $error string [错误描述]
     */
    public static function request($url, $type, $data = false, $timeout = 150, $header = []) {
        $cl = curl_init();
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cl, CURLOPT_HEADER, true);
        curl_setopt($cl, CURLOPT_NOSIGNAL, 1);
        if (count($header) > 0) {
            curl_setopt($cl, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($cl, CURLOPT_NOBODY, 0);
        if ($timeout > 0) {
            curl_setopt($cl, CURLOPT_TIMEOUT_MS, $timeout);
        }
        if ($type == 'POST') {
            curl_setopt($cl, CURLOPT_POST, true);
            curl_setopt($cl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        if ($type == 'GET' && is_array($data)) {
            if (stripos($url, "?") === FALSE) {
                $url .= '?';
            }
            $url .= http_build_query($data);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        $response = curl_exec($cl);
        $status = curl_getinfo($cl);
        $errno  = curl_errno($cl);
        $error = curl_error($cl);
        curl_close($cl);
        if ($errno == 0 && isset($status['http_code'])) {
            $header = substr($response, 0, $status['header_size']);
            $body = substr($response, $status['header_size']);
            return array($body, $header, $status, 0, '');
        } else {
            return array('', '', $status, $errno, $error);
        }
    }

    public static function postRequest($url, $data = false, $timeout = 150) {
        list($body, $header, $status, $errno, $error) = static::request($url, static::$postMode, $data, $timeout);
        if ($errno != 0) {
            \Yii::error("请求[" . $url ."]失败:" .
                "请求参数 " . json_encode(["data" => $data, "header" => $header, "timeout" => $timeout], JSON_UNESCAPED_UNICODE) .
                " | 返回结果 " . json_encode(["status" => $status, "errno" => $errno, "error" => $error, "header" => $header, "body" => $body], JSON_UNESCAPED_UNICODE)
                );
            return false;
        }
        return $body;
    }

    public static function getRequest($url, $data = false, $timeout = 150) {
        list($body, $header, $status, $errno, $error) = static::request($url, static::$getMode, $data, $timeout);
        if ($errno != 0) {
            \Yii::error("请求[" . $url ."]失败:" .
                "请求参数 " . json_encode(["data" => $data, "header" => $header, "timeout" => $timeout], JSON_UNESCAPED_UNICODE) .
                " | 返回结果 " . json_encode(["status" => $status, "errno" => $errno, "error" => $error, "header" => $header, "body" => $body], JSON_UNESCAPED_UNICODE)
            );
            return false;
        }
        return $body;
    }
}