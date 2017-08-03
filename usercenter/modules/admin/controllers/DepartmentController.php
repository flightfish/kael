<?php
namespace usercenter\modules\admin\controllers;

use common\models\Platform;
use common\models\Role;
use common\models\UserCenter;
use usercenter\controllers\BaseController;
use usercenter\modules\admin\models\Departments;
use usercenter\modules\admin\models\User;
use Yii;

class DepartmentController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try{
            $adminList = UserCenter::findListByRole([Role::ROLE_ADMIN,Role::ROLE_DEPARTMENT_ADMIN]);
            $platformList = Platform::findAllList();
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            return $this->renderPartial('index',[
                'adminList'=>$adminList,
                'platformList'=>$platformList
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



    public function actionEditAdmin(){
        try{
            $model = new Departments(['scenario'=>Departments::SCENARIO_EDIT]);
            $model->load($this->loadData);
            $model->validate();
            $model->editAdmin();
            return $this->success();
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionEditDepartment(){
        try{
            $model = new Departments(['scenario'=>Departments::SCENARIO_EDIT_DEPARTMENT]);
            $model->load($this->loadData);
            $model->validate();
            $model->editDepartment();
            return $this->success();
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

}