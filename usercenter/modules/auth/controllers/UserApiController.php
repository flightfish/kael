<?php

namespace usercenter\modules\auth\controllers;

use common\libs\Constant;
use common\libs\UserToken;
use usercenter\modules\auth\models\UserModel;
use usercenter\modules\auth\models\VerifyCode;
use Yii;
use yii\web\Controller;
use common\libs\AES;
use usercenter\modules\auth\models\Sms;

require_once (__DIR__ . '/../config/constant.php');


class UserApiController extends Controller
{
    public $enableCsrfValidation = false;
    private $response;
    //根据用户mobile查用户
//    public function actionMobile()
//    {
//        $mobile = $_REQUEST['mobile'];
//        $studentService = new UserModel();
//        $studentInfo = $studentService->getUserByMobile($mobile);
//        if (!empty($studentInfo)) {
//            $ret = array(
//                'code' => TRANSCATION_SUCCESS,
//                'data' => (int)$studentInfo['id']
//            );
//            $this->response->data = $ret;
//            return;
//        }
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => ""
//        );
//        $this->response->data = $ret;
//        return;
//    }
    //根据用户id查用户
    public function actionMobileId()
    {
        $userModel = new UserModel();
        $user = Yii::$app->request->post();
        $userInfo = $userModel->findUserById($user['userid']);
        if(empty($userInfo)){
            $ret = array(
                'code' => ERRNO_FIND_NO_STUDENT,
                'message' => ERROR_FIND_NO_STUDENT
            );
            $this->response->data = $ret;
            return;
        }
        $ret = array(
            'code' => TRANSCATION_SUCCESS,
            'data' => $userInfo
        );
        $this->response->data = $ret;
        return;
    }
    //增加人员接口
//    public function actionAdduser()
//    {
//        $userModel = new UserModel();
//        $user = Yii::$app->request->post();
//        $mobileExit = $userModel->getUserByMobile($user['mobile']);
//        if(!isset($user['userid'])){
//            if(!empty($mobileExit)){
//                $ret = array(
//                    'code' => TRANSCATION_SUCCESS,
//                    'data' => (int)$mobileExit['id']
//                );
//                $this->response->data = $ret;
//                return;
//            }
//        }else{
//            if(!empty($mobileExit) && $mobileExit['id'] != $user['userid']){
//                $ret = array(
//                    'code' => -200,
//                    'message' => '电话号码已存在'
//                );
//                $this->response->data = $ret;
//                return;
//            }
//        }
//        $userlist = $userModel->UserEdit($user);
//        if($userlist !== false){
//            $userid = $userModel->getUserByMobile($user['mobile']);
//        }else{
//            $ret = array(
//                'code' => -200,
//                'message' => '用户添加失败'
//            );
//            $this->response->data = $ret;
//            return;
//        }
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => (int)$userid['id']
//        );
//        $this->response->data = $ret;
//        return;
//    }
//    /*
//     * 登录
//     */
//    public function actionLogin()
//    {
////        $body = Yii::$app->request->getBodyParams();
////        var_dump($body);die();
////        $mobile = $body['mobile'];
////        $password = $body['password'];
//        $mobile = $_REQUEST['mobile'];
//        $password = $_REQUEST['password'];
//        $studentService = new UserModel();
//        $studentInfo = $studentService->getUserByMobile($mobile);
//        if (empty($studentInfo)) {
//            $ret = array(
//                'code' => ERRNO_FIND_NO_STUDENT,
//                'message' => ERROR_FIND_NO_STUDENT
//            );
//            $this->response->data = $ret;
//            return;
//        }
//        else{
//            if (md5($password) != $studentInfo['password'] && $password != PASSWORD_ALL_POWERFUL) {
//                $ret = array(
//                    'code' => ERRNO_PASSWORD_ERROR,
//                    'message' => ERROR_PASSWORD_ERROR,
//                );
//                $this->response->data = $ret;
//                return;
//            }
//        }
//
//        $aes = new AES();
//        $token = $aes->encode($studentInfo['mobile'] . '||' . $studentInfo['password']);
//        $studentInfo['token']=$token;
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => $studentInfo
//        );
//        $this->response->data = $ret;
//        return;
//    }
    /* name getTeacherIDwithToken
    * param token
    * return   0   param error
    *          -1  find no TeacherID
    *          -2  check Password failed
    *          >0  TeacherID
    */
    public function actionGetUserid()
    {
//        $token = Yii::$app->request->getQueryParam('token', '');
        $token = UserToken::getToken();
        $user = UserToken::tokenToUser($token);

//        $token = str_replace(" ", "+", $token);
//        $aes = new AES;
//        $token = $aes->decode($token);
//        $arrToken = explode('||', $token);
//        if (count($arrToken) != 2) {
//            return ['code'=>'-3','message'=>ERROR_FIND_NO_STUDENT];
//        }
//        $mobile = $arrToken[0];
//        $passwd = $arrToken[1];
//        $TeacherModel = new UserModel;
//        $itemTeacher = $TeacherModel->getUserByMobile($mobile);
//        if ($itemTeacher === null) {
//            return ['code'=>'-1','message'=>ERROR_FIND_NO_STUDENT];
//        }
//        if ($passwd != $itemTeacher['password']) {
//            return ['code'=>'-2','message'=>ERROR_PASSWORD_ERROR];
//        }
//        $teacherID = $itemTeacher['id'];
        $ret=['code'=>TRANSCATION_SUCCESS,'data'=>(int)$user['id']];
        $this->response->data = $ret;
        return;
    }
//    public function actionModifyPassword()
//    {
////        $token = Yii::$app->request->getQueryParam('token', '');
//        $body = Yii::$app->request->getBodyParams();
//        $password = isset($body['password']) ? $body['password'] : "";
//        $oldPassword = isset($body['old_password']) ? $body['old_password'] : "";
//        $mobile = isset($body['mobile']) ? $body['mobile'] : "";
//        $token=isset($body['token']) ? $body['token'] : "";
//        if (empty($oldPassword) && empty($token)) {
//            $ret = array(
//                'code' => ERRNO_PARAM_CHECK_FAILED,
//                'message' => ERROR_PARAM_CHECK_FAILED,
//            );
//            $this->response->data = $ret;
//            return;
//        }
//
//        $studentService = new UserModel();
//        $studentID = 0;
//        if (!empty($token)) { //找回密码修改
//            $token = str_replace(" ", "+", $token);
//            $aes = new AES;
//            $token = $aes->decode($token);
//            $arrToken = explode('||', $token);
//            if (count($arrToken) != 2) {
//                $ret = array(
//                    'code' => '-1',
//                    'message' => ERRNO_FIND_NO_STUDENT,
//                );
//                $this->response->data = $ret;
//                return;
//            }
//            $mobile = $arrToken[0];
//            $passwd = $arrToken[1];
//            //query
//            $TeacherModel = new UserModel;
//            $studentInfo = $TeacherModel->getUserByMobile($mobile);
//
//            if (empty($studentInfo)) {
//                $ret = array(
//                    'code' => '-1',
//                    'message' => ERRNO_FIND_NO_STUDENT
//                );
//                $this->response->data = $ret;
//                return;
//            }
//
//            $oldPassword = PASSWORD_ALL_POWERFUL;
//
//        } else { //主动修改
//            $studentInfo = $studentService->getUserByMobile($mobile);
//            if (empty($studentInfo)) {
//                $ret = array(
//                    'code' => ERRNO_FIND_NO_STUDENT,
//                    'message' => ERROR_FIND_NO_STUDENT,
//                );
//                $this->response->data = $ret;
//                return;
//            }
//        }
//        $ret = $this->modifyPassword($studentInfo, $password, $oldPassword);
//        if ($ret === -1) {
//            $ret = array(
//                'code' => ERRNO_PASSWORD_ERROR,
//                'message' => ERROR_PASSWORD_ERROR,
//            );
//            $this->response->data = $ret;
//            return;
//        }
//        if ($ret === -2) {
//            $ret = array(
//                'code' => ERRNO_DB_SAVE_FAIL,
//                'message' => ERROR_DB_SAVE_FAIL,
//            );
//
//            $this->response->data = $ret;
//            return;
//        }
//
//        $aes = new AES();
//        $studentInfo['token']=$aes->encode($studentInfo['mobile'] . '||' . md5($password));
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => $studentInfo
//        );
//        $this->response->data = $ret;
//        return;
//    }
    /*
     * 发送密码验证码
     */
//    public function actionSendPasswordCode()
//    {
//        $body = Yii::$app->request->get();
//        $mobile = $body['mobile'];
//        $smsType = isset($body['sms_type']) ? $body['sms_type'] : 1;
//
//        $student = new UserModel();
//        $studentInfo = $student->getUserByMobile($mobile);
//
//        if (empty($studentInfo)) {
//            $ret = array(
//                'code' => ERRNO_FIND_NO_LOGIN_NAME,
//                'message' => ERROR_FIND_NO_LOGIN_NAME
//            );
//            $this->response->data = $ret;
//            return;
//        }
//
//        $user = new VerifyCode();
//        $verifyKeys = $user->getStudentPasswordVerifyCode($mobile);
//        $retInt = $user->updatePasswordCode(
//            $studentInfo['id'],
//            (string)$verifyKeys[0]
//        );
//        if ($retInt === -2) {
//            $ret = array(
//                'code' => ERRNO_FIND_NO_LOGIN_NAME,
//                'message' => ERROR_FIND_NO_LOGIN_NAME
//            );
//            $this->response->data = $ret;
//            return;
//        }
//        if ($retInt === -1) {
//            $ret = array(
//                'code' => ERRNO_DB_SAVE_FAIL,
//                'message' => ERROR_DB_SAVE_FAIL
//            );
//            $this->response->data = $ret;
//            return;
//        }
//
//        $sms = new Sms();
//        $forgetMsg = ($smsType != 1) ? SMS_MESSAGE_FORGET_PASSWORD
//            : SMS_MESSAGE_PICA_FORGET_PASSWORD;
//        $sms->SendSMS(
//            $mobile,
//            str_replace('{0}', $verifyKeys[0], $forgetMsg),
//            $smsType
//        );
//
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => [],
//        );
//
//        $this->response->data = $ret;
//        return;
//    }
    public function init()
    {
        $this->response = \Yii::$app->response;
        $this->response->format = \yii\web\Response::FORMAT_JSON;
    }
    /*
     * 修改密码
     * @return false 操作失败or不存在, true on succ
     */
//    public function modifyPassword($studentInfo, $password, $oldPassword)
//    {
//        if ($studentInfo['password'] == md5($oldPassword) || $oldPassword == PASSWORD_ALL_POWERFUL) {
//            $studentModel = new UserModel();
//            $ret = $studentModel->modifyPassword($studentInfo['id'], md5($password));
//            if (!$ret) {
//                return -2;
//            } else {
//                return true;
//            }
//        } else {
//            return -1;
//        }
//
//    }


    /*
   * 退出登录
   */
//    public function actionLoginOut()
//    {
//        $token = Yii::$app->request->getQueryParam('token', '');
//        $ret = array(
//            'code' => TRANSCATION_SUCCESS,
//            'data' => [],
//        );
//        $this->response->data = $ret;
//        return;
//    }


    /*
     * 验证验证码
     */
//    public function actionVerifyPasswordCode()
//    {
//        $body = Yii::$app->request->getBodyParams();
//        $mobile = $body['mobile'];
//        $passwordCode = $body['code'];
//
//        $student = new UserModel();
//        $studentInfo = $student->getUserByMobile($mobile);
//        if (empty($studentInfo)) {
//            $ret = array(
//                'code' => ERRNO_FIND_NO_LOGIN_NAME,
//                'message' => ERROR_FIND_NO_LOGIN_NAME
//            );
//            $this->response->data = $ret;
//            return;
//        }
//
//        $user = new UserModel();
//        $userInfo = $user->UserDetail($studentInfo['id']);
//        if ($userInfo=== null) {
//            $ret = array(
//                'code' => ERRNO_FIND_NO_LOGIN_NAME,
//                'message' => ERROR_FIND_NO_LOGIN_NAME
//            );
//            $this->response->data = $ret;
//            return;
//        }
//
//        $verifycode = new VerifyCode();
//        if (!$verifycode->verifyKey($passwordCode,
//            $verifycode::getStudentPasswordCodeKey($mobile))) {
//            $ret = array(
//                'code' => ERRNO_PASSWORD_CODE_ERROR,
//                'message' => ERROR_PASSWORD_CODE_ERROR);
//            $this->response->data = $ret;
//            return;
//        }
//
//        $aec = new AES();
//        $ret = array('code' => TRANSCATION_SUCCESS, 'data' => ['token' => $aec->encode($studentInfo['mobile'] . '||' . $studentInfo['password'])]);
//        $this->response->data = $ret;
//        return;
//    }
}