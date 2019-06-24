<?php
namespace usercenter\modules\common\controllers;

use common\libs\crypto\DingtalkCrypt;
use common\libs\DingTalkApi;
use common\models\DingLog;
use usercenter\components\exception\Exception;
use usercenter\controllers\BaseController;

class DingController extends BaseController{

    public $event_list = [
        'user_add_org',     //通讯录用户增加
        'user_modify_org',  //通讯录用户更改
        'user_leave_org',   //通讯录用户离职
        'org_admin_add',    //通讯录用户被设为管理员
        'org_admin_remove', //通讯录用户被取消设置管理员
        'org_dept_create',  //通讯录企业部门创建
        'org_dept_modify',  //通讯录企业部门修改
        'org_dept_remove',  //通讯录企业部门删除
        'org_remove',       //企业被解散
        'org_change',       //企业信息发生变更
        'label_user_change',//员工角色信息发生变更
        'label_conf_add',   //增加角色或者角色组
        'label_conf_del',   //删除角色或者角色组
        'label_conf_modify' //修改角色或者角色组
    ];

    private $corpid = 'ding56f88c485c1f3d8e35c2f4657eb6378f'; //公司级编号
    private $token = '123456';
    private $encodingAESKey = '1234567890123456789012345678901234567890123';
    private $nonce = 'nEXhMP4r';

    public function actionRegister(){
        $eventList = \Yii::$app->request->post('event_list',0);
        if(!$eventList){
            $eventList = $this->event_list;
        }else{
            foreach ($eventList as $k=>$event){
                if(!in_array($event,$this->event_list)){
                    unset($eventList[$k]);
                }
            }
        }
        $params = [
            'call_back_tag'=>$eventList,
            'token'=>$this->token,
            'aes_key'=>$this->encodingAESKey,
            'url'=>'http://kael.pdev.knowbox.cn/common/ding/call-back'
        ];
        $info = DingTalkApi::registerCallBack($params);
        return ['msg'=>'操作成功'];
    }

    public function actionCallBack(){
        $eventType = \Yii::$app->request->post('EventType','');
        $ding = new DingtalkCrypt($this->token,$this->encodingAESKey,$this->corpid);
        switch ($eventType){
            case 'check_url':
                $params = [];
                $ding->EncryptMsg('success','',$this->nonce, $params);
//                DingLog::add(['event_type'=>$eventType,'data'=>$params]);
                return $params;
                break;
            default:
                return ['msg'=>'参数：事件类型错误'];
        }
    }
}