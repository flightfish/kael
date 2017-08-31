<?php
namespace usercenter\modules\admin\controllers;

use usercenter\controllers\BaseController;
use usercenter\modules\admin\models\Resource;
use usercenter\modules\admin\models\User;
use Yii;

class UserController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndexUser()
    {
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $roleList = $model->roleList();
            $platformList = $model->platformList();
            $departmentList = $model->departmentListByAdmin();
//            $selectRoleList = [
//                ['role_id'=>0,'role_name'=>'普通用户'],
//                ['role_id'=>2,'role_name'=>'部门管理员'],
//            ];
            $selectRoleList = $roleList;
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            return $this->renderPartial('index',[
                'platformList'=>$platformList,
                'departmentList'=>$departmentList,
                'roleList'=>$roleList,
                'selectRoleList'=>$selectRoleList,
            ]);
        }catch(\Exception $e){
            $this->error($e);
        }
    }

    public function actionIndexPriv()
    {
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $roleList = $model->roleList();
            $platformList = $model->platformList();
            $departmentList = $model->departmentListByAdmin();
            $selectRoleList = [
                ['role_id'=>0,'role_name'=>'普通用户'],
                ['role_id'=>2,'role_name'=>'部门管理员'],
            ];
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            return $this->renderPartial('indexlimit',[
                'platformList'=>$platformList,
                'departmentList'=>$departmentList,
                'roleList'=>$roleList,
                'selectRoleList'=>$selectRoleList,
            ]);
        }catch(\Exception $e){
            $this->error($e);
        }
    }

    public function actionPlatformByDepartmentAdmin(){
        try{
            $model = new User(['scenario'=>User::SCENARIO_PLAT_BY_DEPARTMENT]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->platformListByAdminDepartment();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionList(){
        try{
            $model = new User(['scenario'=>User::SCENARIO_LIST]);
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
            $model = new User(['scenario'=>User::SCENARIO_DEL]);
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
            $model = new User(['scenario'=>User::SCENARIO_EDIT]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->mixAddEdit();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionUpdatePriv(){
        try{
            $model = new User(['scenario'=>User::SCENARIO_EDITPRIV]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->updatePriv();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionDownload(){
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $data = $model->Download();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionUpload(){
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $data = $model->actionImportUser();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionDownloadPriv(){
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $data = $model->downloadPrivNew();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

    public function actionUploadPriv(){
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $data = $model->importUserPriv();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }
}