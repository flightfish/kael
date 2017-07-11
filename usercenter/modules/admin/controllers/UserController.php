<?php
namespace questionmis\modules\admin\controllers;

use questionmis\controllers\BaseController;
use questionmis\modules\admin\models\Resource;
use questionmis\modules\admin\models\User;
use Yii;

class UserController extends BaseController{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        try{
            $model = new User();
            $model->load($this->loadData);
            $model->validate();
            $platformList = $model->platformList();
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
            return $this->renderPartial('index',['platformList'=>$platformList]);
        }catch(\Exception $e){
            $this->error($e);
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

    public function actionCheckMobile(){
        try{
            $model = new User(['scenario'=>User::SCENARIO_CHECKMOBILE]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->checkUserMobile();
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
    public function actionDownload(){
        try{
            $model = new User(['scenario'=>User::SCENARIO_USER_DOWNLOAD]);
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
//            var_dump($_REQUEST);die();
            $model = new User(['scenario'=>User::SCENARIO_USER_UPLOAD]);
            $model->load($this->loadData);
            $model->validate();
            $data = $model->actionImportUser();
            return $this->success($data);
        }catch(\Exception $e){
            return $this->error($e);
        }
    }

}