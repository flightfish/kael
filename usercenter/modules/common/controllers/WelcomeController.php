<?php
namespace usercenter\modules\common\controllers;

use usercenter\modules\common\models\RuKou;
use Yii;
use usercenter\controllers\BaseController;

class WelcomeController extends BaseController
{

    public function actionList()
    {
        try {
            $model = new RuKou(['scenario' => RuKou::SCENARIO_RUKOU]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->RuKou();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    public function actionLoginPlatform(){
        try {
            $model = new RuKou(['scenario' => RuKou::SCENARIO_RUKOU]);
            $model->load($this->loadData);
            $model->platform_id = Yii::$app->request->get('platform_id','');
            $model->validate();
            $url = $model->platformUrlWithLog();
            return $this->redirect($url);
//            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}