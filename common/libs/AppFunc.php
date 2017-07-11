<?php
namespace common\libs;


class AppFunc{


    /**
     * @param $data
     * @return array|int
     * 格式化number
     */
    public static function formatNumber($data){
        if(is_array($data)){
            return array_map('intval',$data);
        }
        return intval($data);
    }

    public static function curlPost($url,$data,$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt ($ch, CURLOPT_HEADER,false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('POST '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('POST '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }

    public static function curlGet($url,$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        \Yii::beginProfile('GET '.$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile('GET '.$url, __METHOD__);
        curl_close($ch);
        return $result;
    }


    public static function curlMethod($method,$url,$data=[],$headers=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//            var_dump(json_encode($data));exit();
        }
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $method = strtoupper($method);
        switch($method){
            case "GET":
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
        \Yii::beginProfile($method." ".$url, __METHOD__);
        $result = curl_exec($ch);
        \Yii::endProfile($method . " ".$url, __METHOD__);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'code'=>$code,
            'result'=>$result,
        ];
    }


    public static function mail($userName,$title,$htmlText){
        try{
            $urlhost = 'http://' . $_SERVER['HTTP_HOST'];
            $htmlText = $htmlText . "操作人：$userName<br/>操作网址：{$urlhost}";
            $mail= \Yii::$app->mailer->compose();
            $mail->setTo(\Yii::$app->params['tiku_mail'])
                ->setFrom(\Yii::$app->params['sendFrom'])
                ->setSubject($title)
                ->setHtmlBody($htmlText);    //发布可以带html标签的文本
            //        $mail->setTextBody($htmlText);   //发布纯文字文本
            $ret = $mail->send();
            return $ret;
        }catch(\Exception $e){
            return false;
        }

    }



    public static function joinRecursion($split,$array){
        foreach($array as $k=>$v){
            is_array($v) && $array[$k] = self::joinRecursion($split,$v);
        }
        return join($split,$array);
    }


    /**
     * @param $str
     * @param array $urlList
     * @return array [ [etag:"",bucket:"",url:""] ];
     */
    public static function getQiniuResource($str,$urlList = [])
    {
        if (is_array($str)) {
            $str = self::joinRecursion(',', $str);
        }
        $infoUrl = "http://entrystore.test.knowbox.cn/common/qiniu/info?key=%s&bucket=%s&token";
        if (empty($urlList)) {
            $urlList = [
                //tkimage
                'http\:\/\/7xohdn.com2.z0.glb.qiniucdn.com\/' => 'tiku-img',
                'http\:\/\/7xohdn.com1.z0.glb.clouddn.com\/' => 'tiku-img',
                'http\:\/\/7xohdn.com2.z0.glb.clouddn.com\/' => 'tiku-img',
                'https\:\/\/tikuqiniu.knowbox.cn\/' => 'tiku-img',
                //knowbox
                'http\:\/\/7xjnvd.com2.z0.glb.qiniucdn.com\/' => 'knowbox',
                'http\:\/\/7xjnvd.com1.z0.glb.clouddn.com\/' => 'knowbox',
                'http\:\/\/7xjnvd.com2.z0.glb.clouddn.com\/' => 'knowbox',
                'https\:\/\/knowboxqiniu.knowbox.cn\/' => 'knowbox',
                //audio
                'http\:\/\/7xjqro.com1.z0.glb.clouddn.com\/' => 'knowbox-audio',
                'http\:\/\/7xjqro.com2.z0.glb.clouddn.com\/' => 'knowbox-audio',
                'http\:\/\/7xjqro.com2.z0.glb.qiniucdn.com\/' => 'knowbox-audio',
                //data
                'http\:\/\/ohv58wlkm.bkt.clouddn.com\/' => 'knowbox-data',
                //abservice
                'http\:\/\/ofyjiu406.bkt.clouddn.com\/' => 'abservice',
                'https\:\/\/abserviceqiniu.knowbox.cn\/' => 'abservice',
                //test
                'http\:\/\/7xkdpi.com1.z0.glb.clouddn.com\/' => 'knowbox-test',
                'http\:\/\/7xkdpi.com2.z0.glb.clouddn.com\/' => 'knowbox-test',
                'http\:\/\/7xkdpi.com2.z0.glb.qiniucdn.com\/' => 'knowbox-test',
                //susuan
                'http\:\/\/7xlbxm.com1.z0.glb.clouddn.com\/' => 'susuan',
                'http\:\/\/7xlbxm.com2.z0.glb.clouddn.com\/' => 'susuan',
                'http\:\/\/7xlbxm.com2.z0.glb.qiniucdn.com\/' => 'susuan',
                'https\:\/\/susuanqiniu.knowbox.cn\/' => 'susuan',
            ];
        }
        $matchList = [];
        foreach ($urlList as $url => $bucket) {
//            preg_match_all('/'.$url.'[^,"\']*/is', $str, $matchs);
            preg_match_all('/' . $url . '([^,\?\&\"\']*)/is', $str, $matchs);
            if (!empty($matchs[0])) {
                foreach ($matchs[0] as $index => $filename) {
                    if (stripos($filename, 'edu_fillin.png') !== false) {
                        continue;
                    }
                    $qiniuKey = $matchs[1][$index];
                    $info = AppFunc::curlGet(sprintf($infoUrl, $qiniuKey, $bucket));
                    $info = json_decode($info, true);
                    if (isset($info['code']) && $info['code'] == 0) {
                        $matchOne = [
                            'etag' => $info['data']['hash'],
                            'bucket' => $bucket,
                            'url' => $filename,
                            'size' => $info['data']['fsize'],
                        ];
                        array_push($matchList, $matchOne);
                    } else {
                        echo "=====ERROR========" . $str . "\n\n";
                        continue;
                    }
//                    $fhandler = fopen($filename, 'r');
//                    $sha1Buf = [];
//                    $fdata = file_get_contents($filename);
//                    $fsize = strlen($fdata);
//                    $blockCnt = ceil($fsize/(\Qiniu\Config::BLOCK_SIZE));
//                    if ($blockCnt <= 1) {
//                        array_push($sha1Buf, 0x16);
//                        $fdata = stream_get_contents($fhandler, \Qiniu\Config::BLOCK_SIZE);
//                        $sha1Str = sha1($fdata, true);
//                        $sha1Code = unpack('C*', $sha1Str);
//                        $sha1Buf = array_merge($sha1Buf, $sha1Code);
//                    } else {
//                        array_push($sha1Buf, 0x96);
//                        $sha1BlockBuf = array();
//                        for ($i=0; $i < $blockCnt; $i++) {
//                            $fdata = stream_get_contents($fhandler, \Qiniu\Config::BLOCK_SIZE,$i * \Qiniu\Config::BLOCK_SIZE);
//                            list($sha1Code, $err) = \Qiniu\Etag::calcSha1($fdata);
//                            $sha1BlockBuf = array_merge($sha1BlockBuf, $sha1Code);
//                        }
//                        $tmpData = \Qiniu\Etag::packArray('C*', $sha1BlockBuf);
//                        list($sha1Final, $_err) = \Qiniu\Etag::calcSha1($tmpData);
//                        $sha1Buf = array_merge($sha1Buf, $sha1Final);
//                    }
//                    $matchOne = [
////                        'etag'=> \Qiniu\base64_urlSafeEncode(\Qiniu\Etag::packArray('C*', $sha1Buf)),
//                        'etag'=> \Qiniu\base64_urlSafeEncode(\Qiniu\Etag::packArray('C*', $sha1Buf)),
//                        'bucket' => $bucket,
//                        'url'=> $filename,
//                        'size'=> $fsize,
//                    ];
//                    array_push($matchList,$matchOne);
                }
            }
        }
        return $matchList;
    }

    /**
     * 删除指定标签
     *
     * @param array $tags     删除的标签  数组形式
     * @param string $str     html字符串
     * @param bool $delConent   true删除标签覆盖的内容content
     * @return mixed
     */
    public static function stripHtmlTags($str,$tags = [], $delConent = false){
        if(empty($tags)){
            $tags = ['p','span','div'];
        }
        $html = [];
        // 是否保留标签内的text字符
        if($delConent){
            foreach ($tags as $tag) {
                $html[] = '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is';
            }
        }else{
            foreach ($tags as $tag) {
                $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/is";
            }
        }
        $data = preg_replace($html, '', $str);
        return $data;
    }

    public static function stripSusuanAnswer($str){
        $tags = ['p','span','div'];
        $str = self::stripHtmlTags($str,$tags);
        $str = str_replace('&lt;','<',$str);
        $str = str_replace('&gt;','>',$str);
        $str = trim($str);
        return $str;
    }
}
