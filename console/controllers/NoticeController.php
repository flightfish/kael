<?php
namespace console\controllers;


use common\models\CallbackQueue;
use common\models\CallbackRegister;
use common\models\RelateUserPlatform;
use Yii;
use yii\console\Controller;


class NoticeController extends Controller
{
    public function actionQueuePlatformAdd(){
        if(exec('ps -ef|grep "notice/queue-platform-add"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running\n";
            exit();
        }

        $list = RelateUserPlatform::find()
            ->where(
                [
                    'or',
                    ['notice_status_add'=>0,'status'=>0],
                    ['notice_status_del'=>0,'status'=>1]
                ])
            ->orderBy('id asc')
            ->asArray(true)
            ->all();

        if(empty($list)){
            echo "empty new\n";
            exit();
        }

        $callbackUrlsAdd = CallbackRegister::findAllList(CallbackRegister::NOTICE_TYPE_ADDPRIV);
        $callbackUrlsAddByPlatform  = [];
        foreach ($callbackUrlsAdd as $v){
            $callbackUrlsAddByPlatform[$v['platform_id']][] = $v['notice_url'];
        }

        $callbackUrlsDel = CallbackRegister::findAllList(CallbackRegister::NOTICE_TYPE_DELPRIV);
        $callbackUrlsDelByPlatform  = [];
        foreach ($callbackUrlsDel as $v){
            $callbackUrlsDelByPlatform[$v['platform_id']][] = $v['notice_url'];
        }

        foreach ($list as $v){
            $platformId = $v['platform_id'];
            if($v['status'] == 0 && $v['notice_status_add'] == 0){
                if(!empty($callbackUrlsAddByPlatform[$v['platform_id']])){
                    CallbackQueue::batchInsertAll(
                        CallbackQueue::getDb(),
                        ['platform_id','notice_url','notice_type','notice_comment'],
                        array_map(function($noticeUrl)use($platformId){
                            return [$platformId,$noticeUrl,CallbackRegister::NOTICE_TYPE_ADDPRIV,'新增权限'];
                            },$callbackUrlsAddByPlatform[$v['platform_id']])
                    );
                    RelateUserPlatform::updateAll(['notice_status_add'=>1],['relate_id'=>$v['relate_id']]);
                }else{
                    RelateUserPlatform::updateAll(['notice_status_add'=>2],['relate_id'=>$v['relate_id']]);
                }
            }elseif($v['status'] == 1 && $v['notice_status_del'] == 0){
                if(!empty($callbackUrlsDelByPlatform[$v['platform_id']])){
                    CallbackQueue::batchInsertAll(
                        CallbackQueue::getDb(),
                        ['platform_id','notice_url','notice_type','notice_comment'],
                        array_map(function($noticeUrl)use($platformId){
                            return [$platformId,$noticeUrl,CallbackRegister::NOTICE_TYPE_DELPRIV,'删除权限'];
                        },$callbackUrlsDelByPlatform[$v['platform_id']])
                    );
                    RelateUserPlatform::updateAll(['notice_status_del'=>1],['relate_id'=>$v['relate_id']]);
                }else{
                    RelateUserPlatform::updateAll(['notice_status_del'=>2],['relate_id'=>$v['relate_id']]);
                }
            }
        }

    }

    public function actionQueuePlatformExec(){
        if(exec('ps -ef|grep "notice/queue-platform-exec"|grep -v grep | grep -v cd | grep -v "/bin/sh"  |wc -l') > 1){
            echo "is_running";
            exit();
        }

    }


}
