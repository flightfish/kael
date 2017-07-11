<?php
namespace questionmis\modules\admin\controllers;

use questionmis\modules\admin\models\RuKou;
use Yii;
use questionmis\controllers\BaseController;

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
}