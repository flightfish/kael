<?php
namespace usercenter\modules\admin\controllers;

use common\models\Role;
use common\models\UserCenter;
use usercenter\controllers\BaseController;
use usercenter\modules\admin\models\Departments;
use usercenter\modules\admin\models\Resource;
use usercenter\modules\admin\models\User;
use Yii;

class DepartmentController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try{
            $adminList = UserCenter::findListByRole([Role::ROLE_ADMIN,Role::ROLE_DEPARTMENT_ADMIN]);
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            return $this->renderPartial('index',[
                'adminList'=>$adminList
            ]);
        }catch(\Exception $e){
            $this->error($e);
        }
    }


    public function actionList(){
        try{
            $model = new Departments(['scenario'=>Departments::SCENARIO_LIST]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->pagelist();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionDel(){
        try{
            $model = new Departments(['scenario'=>Departments::SCENARIO_DEL]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->del();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }



    public function actionEdit(){
        try{
            $model = new Departments(['scenario'=>User::SCENARIO_EDIT]);
            $model->load($this->loadData);
            $model->validate();
            $model->edit();
            return $this->success();
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

}