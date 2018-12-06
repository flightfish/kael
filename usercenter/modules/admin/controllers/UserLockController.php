<?php
namespace usercenter\modules\admin\controllers;

use usercenter\controllers\BaseController;
use Yii;
use usercenter\modules\admin\models\UserLock;

class UserLockController extends BaseController{

    public $enableCsrfValidation = false;
    public $layout = 'main';

    public function actionIndex()
    {
        $this->response->format = \yii\web\Response::FORMAT_HTML;
        return $this->render('index');
    }

    //解锁
    public function actionLockList()
    {
        try {
            $model = new UserLock(['scenario' => UserLock::SCENARIO_LIST]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->lockList();
            return $ret;
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    //解锁
    public function actionUnlock()
    {
        try {
            $model = new UserLock(['scenario' => UserLock::SCENARIO_UNLOCK]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->userUnlock();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}