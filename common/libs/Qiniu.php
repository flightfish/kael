<?php
namespace common\libs;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\db\Exception;


class Qiniu {

    const ACCESS_KEY = 'nJL7e6J3VAIC5j4DqE-KKUeOv2LkGTaN4YjXBw7F';
    const SECRET_KEY = 'n4Yt7k2bNwvA7rS4ETENhl_cGhnsS9hUrGFamwvX';

    const BUCKET = 'innerplatform';
    const DOWNLOAD_URL = 'http://ov2iw51h2.bkt.clouddn.com/';

    const UPLOAD_URL = "http://upload.qiniu.com";


    public static function getUploadToken(){
        $auth = new Auth(self::ACCESS_KEY, self::SECRET_KEY);
        $bucket = self::BUCKET;

        $policy = [
            'insertOnly'=>1,
        ];
        $token = $auth->uploadToken($bucket,null,600,$policy);
        $ret = ['uptoken'=>$token];
        return $ret;
    }


    public static function uploadFile($filepath,$key){
        $token = self::getUploadToken();
        $token = $token['uptoken'];

        $uploadManager=new UploadManager();
        $type='image/x-png';

        list($ret,$err)=$uploadManager->putFile($token,$key,$filepath,null,$type,false);

        if($err){//上传失败
            throw new Exception("文件上传失败");
        }else{//成功
            //添加信息到数据库
            return (self::DOWNLOAD_URL).$ret['key'];
        }
    }

}
