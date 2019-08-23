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

    public function actionAll(){
        $ret = EmailApi::getUserListAll();
        return $ret;
    }

}