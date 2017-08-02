<?php

namespace usercenter\modules\auth\models;
use common\libs\Constant;
use common\libs\UserToken;
use common\models\Platform;
use common\models\RelateDepartmentPlatform;
use common\models\RelateUserPlatform;
use common\libs\AES;
use common\models\CommonUser;
use common\models\LogAuthUser;
use usercenter\components\exception\Exception;
use usercenter\models\RequestBaseModel;
use Yii;
class CommonApi extends RequestBaseModel {


    public $page=1;
    public $pagesize=20;
    public $userId=[1,2,3];
    public $groupId;
    public $password='123456';
    public $name;
    public $mobile;
    public $sex;
    public $idcard;
    public $bank_name;
    public $bank_deposit;
    public $bank_area;
    public $bank_account;
    public $free_state;
    public $user_mobile = "";
    public $user_pass = "";
    public $again_pass = "";
    public $old_pass = "";
    public $code;
    public $type;
    public $adminId;
    public $user_source;
    public $user_type;
    public $subject = -1;
    public $grade_part = -1;
    const GROUP_TYPE_ENTRY = 1;
    const GROUP_TYPE_CHECK = 2;
    const SCENARIO_USERLIST = "SCENARIO_USERLIST";
    const SCENARIO_ADDUSER = "SCENARIO_ADDUSER";
    const SCENARIO_EDITUSER = "SCENARIO_EDITUSER";
    const SCENARIO_DELUSER = "SCENARIO_DELUSER";
    const SCENARIO_LOGIN = "SCENARIO_LOGIN";
    const SCENARIO_LOGIN_OUT = "SCENARIO_LOGIN_OUT";
    const CHANGE_PASSWORD = "CHANGE_PASSWORD";
    const FIND_PASSWORD = "FIND_PASSWORD";
    const SEND_PASS_CODE = "SEND_PASS_CODE";
    const VERIFY_PASS_CODE = "VERIFY_PASS_CODE";
    const SCENARIO_USER_BYTOKEN = "SCENARIO_USER_BYTOKEN";
    const SCENARIO_USER_BYID = "SCENARIO_USER_BYID";
    const SCENARIO_USER_BYMOBILE = "SCENARIO_USER_BYMOBILE";
    const SCENARIO_CHECK_PLATFORM_AUTH = "SCENARIO_CHECK_PLATFORM_AUTH";
    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        $scenarios[self::SCENARIO_USERLIST] = ['token','userId'];
        $scenarios[self::SCENARIO_ADDUSER] = ['token','password','name','mobile','sex','idcard','bank_name','bank_deposit','bank_area','bank_account','adminId','user_source','user_type','subject','grade_part'];
        $scenarios[self::SCENARIO_EDITUSER] = ['userId','token','name','mobile','sex','idcard','bank_name','bank_deposit','bank_area','bank_account','subject','grade_part'];
        $scenarios[self::SCENARIO_DELUSER] = ['userId','groupId','token'];
        $scenarios[self::SCENARIO_LOGIN] = ['user_mobile','user_pass'];
        $scenarios[self::CHANGE_PASSWORD] = ['token','user_mobile','user_pass','old_pass','again_pass'];
        $scenarios[self::FIND_PASSWORD] = ['token','user_pass'];
        $scenarios[self::SEND_PASS_CODE] = ['user_mobile'];
        $scenarios[self::VERIFY_PASS_CODE] = ['user_mobile','code'];
        $scenarios[self::SCENARIO_LOGIN_OUT] = ['token'];
        $scenarios[self::SCENARIO_USER_BYTOKEN] = ['token'];
        $scenarios[self::SCENARIO_USER_BYID] = ['token','userId'];
        $scenarios[self::SCENARIO_USER_BYMOBILE] = ['token','mobile'];
        $scenarios[self::SCENARIO_CHECK_PLATFORM_AUTH] = ['token','auth_platform_id'];
        return $scenarios;
    }

    public function rules()
    {
        return array_merge([
            [['page','pagesize'], 'integer'],
            [['userId'], 'required','on'=>self::SCENARIO_USERLIST],
            [['name','mobile','sex','adminId','user_source','user_type'], 'required','on'=>self::SCENARIO_ADDUSER],
            [['userId','token','name','mobile','sex'], 'required','on'=>self::SCENARIO_EDITUSER],
            [['userId','token'], 'required','on'=>self::SCENARIO_DELUSER],
            [['user_mobile','user_pass'], 'required','on'=>self::SCENARIO_LOGIN],
            [['user_pass'], 'required','on'=>self::FIND_PASSWORD],
            [['user_mobile'], 'required','on'=>self::SEND_PASS_CODE],
            [['user_mobile','code'], 'required','on'=>self::VERIFY_PASS_CODE],
            [['userId'], 'required','on'=>self::SCENARIO_USER_BYID],
            [['mobile'], 'required','on'=>self::SCENARIO_USER_BYMOBILE],
            [['user_pass'], 'required','on'=>self::CHANGE_PASSWORD],
        ],parent::rules());
    }
    /**
     * 人员列表
     */
    public function userList(){
        $filter=[
//            'id'=>$this->userId
        ];
        $select = ['id','name','mobile','sex','idcard','bank_name','bank_account'];
        $list = CommonUser::findListByPage($filter,$select);
        $count = CommonUser::findCountByFilter($filter);
        return $list;
    }

    /**
     * 增加人员
     */
    public function addUser(){
//        parent::getUser();
        $data=[
            'username'=>$this->name,
            'admin_id'=>$this->adminId,
            'password'=>$this->password,
            'mobile'=>$this->mobile,
            'sex'=>$this->sex,
            'user_source'=>$this->user_source,
            'user_type'=>$this->user_type,
            'idcard'=>$this->idcard,
            'bank_name'=>$this->bank_name,
            'bank_deposit'=>$this->bank_deposit,
            'bank_area'=>$this->bank_area,
            'bank_account'=>$this->bank_account,
            'subject'=>$this->subject,
            'grade_part'=>$this->grade_part,
        ];

//        $MobileOnly = CommonUser::findByMobile($this->mobile);
//        if(!empty($MobileOnly)){
//            throw new Exception(Exception::MOBILE_NOT_ONLY,Exception::ERROR_COMMON);
//        }
        $user = CommonUser::addUser($data);
        if($user === false){
            throw new Exception(Exception::ADD_USER_FILE,Exception::ERROR_COMMON);
        }
        //记日志
        $userLog = self::$_user;
        $addUserId = Yii::$app->db->getLastInsertID();
        LogAuthUser::LogUser($userLog['id'],$addUserId,LogAuthUser::OP_ADD_USER,$data);
        return $addUserId;
    }
    /**
     * 编辑人员
     */
    public function editUser(){
//        parent::getUser();
        $data=[
            'username'=>$this->name,
            'id'=>$this->userId,
            'admin_id'=>$this->adminId,
            'mobile'=>$this->mobile,
            'sex'=>$this->sex,
            'idcard'=>$this->idcard,
            'bank_name'=>$this->bank_name,
            'bank_deposit'=>$this->bank_deposit,
            'bank_area'=>$this->bank_area,
            'bank_account'=>$this->bank_account,
            'subject'=>$this->subject,
            'grade_part'=>$this->gradePart,
        ];

        $userExist = CommonUser::findByID($this->userId,true);
        if(empty($userExist)){
            throw new Exception(Exception::EDIT_USER_EXIST,Exception::ERROR_COMMON);
        }
        $MobileOnly = CommonUser::findByMobile($this->mobile);
        if(!empty($MobileOnly) && $MobileOnly['id']!=$this->userId){
            throw new Exception(Exception::MOBILE_NOT_ONLY,Exception::ERROR_COMMON);
        }
        $user = CommonUser::editUser($data);
        if($user === false){
            throw new Exception(Exception::EDIT_USER_FAIL,Exception::ERROR_COMMON);
        }
        //记日志
        $userLog = self::$_user;
        $addUserId = Yii::$app->db->getLastInsertID();
        LogAuthUser::LogUser($userLog['id'],$addUserId,LogAuthUser::OP_ADD_USER,$data);
        return $user;
    }
    /**
     * 根据token获取人员信息列表
     */
    public function UserListByToken(){
//        parent::getUser();
//        $user = self::$_user;
//        return $user;
        return $this->user;
    }
    /**
     * 根据userid获取人员信息列表
     */
    public function UserListById(){
        $user = CommonUser::findByID($this->userId,true);
        if(empty($user)){
            throw new Exception(Exception::EDIT_USER_EXIST,Exception::ERROR_COMMON);
        }
        return $user;
    }
    /**
     * 根据mobile获取人员信息列表
     */
    public function UserListByMobile(){
        $user = CommonUser::findAllByMobile($this->mobile);
//        if(empty($user)){
//            throw new Exception(Exception::EDIT_USER_EXIST,Exception::ERROR_COMMON);
//        }
        return $user;
    }

//    public function encodeToken($mobile,$password){
//        $aes = new AES;
//        $token = $aes->encode($mobile . '||' . $password);
//        return $token;
//    }

    //登录
    public function Login(){
        $user = CommonUser::findByMobile($this->user_mobile);
        if (empty($user)) {
            throw new Exception(Exception::MOBILE_CHANGE, Exception::ERROR_COMMON);
        } else {
            if (md5($this->user_pass) != $user['password'] && $this->user_pass != PASSWORD_ALL_POWERFUL) {
                throw new Exception(Exception::USER_PASS_WRONG, Exception::ERROR_COMMON);
            }
        }
        CommonUser::updateAll(['login_ip'=>strval(Yii::$app->request->userIP)],['id'=>$user['id']]);
        $token = UserToken::userToToken($user);
        $user['token']=$token;
        //记日志
        LogAuthUser::LogLogin($user['id'],LogAuthUser::OP_LOGIN,$user);
        setcookie(Constant::LOGIN_TOKEN_NAME, $token, time() + Constant::LOGIN_TOKEN_TIME, '/', Constant::LOGIN_TOKEN_HOST);
//        !isset($_COOKIE['token']) && setcookie('token', $token, time() + 7*24*3600, '/', Constant::LOGIN_TOKEN_HOST);
        return ['token'=>$token];
    }

    //登出
    public function LoginOut(){
        //记日志
//        parent::getUser();
//        $userLog = self::$_user;
//        $data = [
//            'user_id'=>$userLog['id'],
//            'mobile'=>$userLog['mobile'],
//            'name'=>$userLog['name'],
//        ];
//        LogAuthUser::LogLogin($userLog['id'],LogAuthUser::OP_LOGIN_OUT,$data);
        setcookie(Constant::LOGIN_TOKEN_NAME, "", time() - 3600, '/', Constant::LOGIN_TOKEN_HOST);
        return [];
    }


    //修改密码
    public function ChangePass(){
        if (empty($this->user_pass) && empty($this->token)) {
            throw new Exception(Exception::MOBILE_CHANGE, Exception::ERROR_COMMON);
        }
        if (!empty($this->token) && empty($this->old_pass)) { //找回密码修改
//            parent::getUser();
//            $user = self::$_user;
            $user = $this->user;
            $this->old_pass = PASSWORD_ALL_POWERFUL;
        } else { //主动修改
//            parent::getUser();
//            $user = self::$_user;
            $user = $this->user;
            if($this->user_pass != $this->again_pass){
                throw new Exception(Exception::NEW_AGAIN_WRONG, Exception::ERROR_COMMON);
            }
        }

        $ret = $this->modifyPassword($user, $this->user_pass, $this->old_pass);
        $user['password'] = md5($this->user_pass);
        $token = UserToken::userToToken($user);
        $user['token']=$token;
        //记日志
        LogAuthUser::LogLogin($user['id'],LogAuthUser::OP_CHANGE_PASS,$user);
        setcookie(Constant::LOGIN_TOKEN_NAME, $token, time() + Constant::LOGIN_TOKEN_TIME, '/', Constant::LOGIN_TOKEN_HOST);
        return ['token'=>$token];
    }
    /*
     * 修改密码
     */
    public function modifyPassword($userInfo, $password, $oldPassword)
    {
        if ($userInfo['password'] == md5($oldPassword) || $oldPassword == PASSWORD_ALL_POWERFUL) {
            $ret = CommonUser::modifyPassword($userInfo['id'], md5($password));
            return true;
//            if (!$ret) {
//                throw new Exception(Exception::CHANGE_PASS_WRONG, Exception::ERROR_COMMON);
//            } else {
//                return true;
//            }
        } else {
            throw new Exception(Exception::OLD_PASS_WRONG, Exception::ERROR_COMMON);
        }

    }
    //发送验证码
    public function SendPasswordCode(){
        $user = CommonUser::findByMobile($this->user_mobile);
        if(empty($user)){
            throw new Exception(Exception::MOBILE_NOT_FIND,Exception::ERROR_COMMON);
        }
        $smsType = isset($body['sms_type']) ? $body['sms_type'] : 1;

        $verifycode = new VerifyCode();

        $verifyKeys = $verifycode->getStudentPasswordVerifyCode($this->user_mobile);

        $sms = new Sms();
        $forgetMsg = ($smsType != 1) ? SMS_MESSAGE_FORGET_PASSWORD
            : SMS_MESSAGE_PICA_FORGET_PASSWORD;
        $res = $sms->SendSMS(
            $this->user_mobile,
            str_replace('{0}', $verifyKeys[0], $forgetMsg),
            $smsType
        );
        //记日志
        LogAuthUser::LogLogin($this->user_mobile,LogAuthUser::OP_SEND_CODE,$res);
        return $res;
    }
    //验证验证码
    public function VerifyPasswordCode(){
        $user = CommonUser::findByMobile($this->user_mobile);
        if(empty($user)){
            throw new Exception(Exception::MOBILE_NOT_FIND,Exception::ERROR_COMMON);
        }
        $verifycode = new VerifyCode();
        if (!$verifycode->verifyKey($this->code,$verifycode::getStudentPasswordCodeKey($this->user_mobile))) {
            throw new Exception(Exception::CODE_WRONG,Exception::ERROR_COMMON);
        }
        $token = UserToken::userToToken($user);//$this->encodeToken($user['mobile'],$user['password']);
        //记日志
        LogAuthUser::LogLogin($this->user_mobile,LogAuthUser::OP_VERIFY_CODE,$token);
        setcookie(Constant::LOGIN_TOKEN_NAME, $token, time() + Constant::LOGIN_TOKEN_TIME, '/', Constant::LOGIN_TOKEN_HOST);
        return ['token'=>$token];
    }

    //校验权限
    public function checkPlatformAuth(){

        $sourceUrl = Yii::$app->request->referrer;
        if(empty($sourceUrl)){
            throw new Exception('权限不足，请联系系统管理员',Exception::ERROR_COMMON);
        }
        $sourceUrlArr = parse_url($sourceUrl);
        $host = $sourceUrlArr['host'];
        if(empty($host)){
            throw new Exception('权限不足，请联系系统管理员',Exception::ERROR_COMMON);
        }
        $platformInfo = Platform::findOneByHost($host,$this->auth_platform_id);
        if(empty($platformInfo)){
            throw new Exception('权限不足，请联系管理员',Exception::ERROR_COMMON);
        }
        //ip限定
        $serverIPList = explode(',',$platformInfo['server_ips']);
        $clientIPAllow = explode(',',$platformInfo['allow_ips']);
        $serverIP = Yii::$app->request->getUserIP();
        $clientIP = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : "";
        if(!empty($platformInfo['server_ips']) && !in_array($serverIP,$serverIPList)){
            throw new Exception('发生异常，请联系管理员',Exception::ERROR_COMMON);
        }
        if(!empty($platformInfo['allow_ips']) && !in_array($clientIP,$clientIPAllow)){
            throw new Exception('无权访问，请联系管理员',Exception::ERROR_COMMON);
        }
        //判断部门权限
        $relate = RelateDepartmentPlatform::findListByDepartmentPlatform($this->user['department_id'],$platformInfo['platform_id']);
        if(empty($relate)){
            throw new Exception('权限不足，请确认有权限后重试',Exception::ERROR_COMMON);
        }
        $relate = RelateUserPlatform::findListByUserPlatform($this->user['id'],$platformInfo['platform_id']);
        if(empty($relate)){
            throw new Exception('您的权限不足，请确认有权限后重试',Exception::ERROR_COMMON);
        }

        return $this->user;
    }
}