<?php
namespace usercenter\modules\common\controllers;

use common\libs\EmailApi;
use Yii;
use usercenter\controllers\BaseController;

class EmailController extends BaseController
{

    public function actionList()
    {
        $ret = EmailApi::getDepartmentList();
        return $ret;
    }

    public function actionAllUser(){
        $ret = EmailApi::getUserListAll();
        $userList = array_column($ret['userlist'],'name','userid');
        $count = count($ret['userlist']);
        return ['count'=>$count,'list'=>$userList];
    }

}