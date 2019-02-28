<?php
namespace usercenter\modules\auth\controllers;

use common\libs\Constant;
use common\libs\UserToken;
use usercenter\components\exception\Exception;
use usercenter\modules\auth\models\CommonApi;
use usercenter\controllers\BaseController;
use Yii;
use yii\web\Controller;

require_once (__DIR__ . '/../config/constant.php');

class CommonApiController extends BaseController
{
    public function actionUserList()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_USERLIST]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->userList();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
    /**
     * 根据token获取人员信息列表
     */
    public function actionUserListByToken()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_USER_BYTOKEN]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->UserListByToken();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
    /**
     * 根据userid获取人员信息列表
     */
    public function actionUserListById()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_USER_BYID]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->UserListById();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
    /**
     * 根据mobile获取人员信息列表
     */
    public function actionUserListByMobile()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_USER_BYMOBILE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->UserListByMobile();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
    //新增人员
    public function actionAddUser()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_ADDUSER]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->addUser();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    //编辑人员
    public function actionEditUser()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_EDITUSER]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->editUser();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    //删除人员
    public function actionDelUser()
    {
        try {
            $model = new CommonApi(['scenario' => CommonApi::SCENARIO_DELUSER]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->delUser();
            return $this->success($ret);
        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    //登录接口
    public function actionLogin()
    {
        try{
            $model = new CommonApi(['scenario'=>CommonApi::SCENARIO_LOGIN]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->login();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionIsLogin(){
        try{
            $token = UserToken::getToken();
            if(empty($token)){
                throw new Exception(Exception::NOT_LOGIN_MSG,Exception::NOT_LOGIN_CODE);
            }
            return $this->success();
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }
    //登出接口
    public function actionLoginOut()
    {
        try{
            $model = new CommonApi(['scenario'=>CommonApi::SCENARIO_LOGIN_OUT]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->LoginOut();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }
    //修改密码接口
    public function actionChangePassword()
    {
        try{
            $model = new CommonApi(['scenario'=>CommonApi::CHANGE_PASSWORD]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->ChangePass();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    //发送验证码
    public function actionSendPasswordCode()
    {
        try{
            $model = new CommonApi(['scenario'=>CommonApi::SEND_PASS_CODE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->SendPasswordCode();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }
    //验证验证码
    public function actionVerifyPasswordCode()
    {
        try{
            $model = new CommonApi(['scenario'=>CommonApi::VERIFY_PASS_CODE]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->VerifyPasswordCode();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionCheckPlatformAuth(){
        try{
            $model = new CommonApi(['scenario'=>CommonApi::SCENARIO_CHECK_PLATFORM_AUTH]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->checkPlatformAuth();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }

    public function actionCheckAuth(){
        try{
            $model = new CommonApi(['scenario'=>CommonApi::SCENARIO_CHECK_PLATFORM_AUTH]);
            $model->load($this->loadData);
            $model->validate();
            $ret = $model->checkAuth();
            return $this->success($ret);
        }catch(\Exception $exception){
            return $this->error($exception);
        }
    }
}