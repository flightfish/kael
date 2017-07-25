<?php
namespace usercenter\components\exception;

class Exception extends \yii\base\Exception {

    const SYSTEM_ERROR_CODE  = 500;
    const SYSTEM_ERROR_MSG = '服务器异常';

    const COMMON_ERROR_CODE_PARAM_VALIDATE_FAIL = 501;

    const API_USERCENTER_ERROR = 1001;

    const NOT_LOGIN_CODE = -1;
    const NOT_LOGIN_MSG = "用户尚未登录,请登陆后操作";

    const COMMON_USER_NOT_EXIST_CODE = 404;
    const COMMON_USER_NOT_EXIST_MSG = "用户不存在";

    const PRIV_NOT_ENOUGH_CODE = 403;
    const PRIV_NOT_ENOUGH_MSG = "权限不足";

    const SUBJECT_NOT_MATCH_CODE = 601;
    const SUBJECT_NOT_MATCH_MSG = "学科学段不匹配";

    const ERROR_NOT_EXIST = 201;//不存在错误
    const ERROR_COMMON = 204;//通用错误
    const LOG_ERROR = 205;

    const EDIT_GROUP_USER_EXIT_CODE = 300;
    const EDIT_GROUP_CHILD_EXIT_CODE = 301;
    const MOBILE_NOT_ONLY = "手机号码已存在";
    const MOBILE_NOT_FIND = "手机号码不存在";
    const ADD_USER_FILE = "添加人员失败";
    const ADD_USER_ROLE_FILE = "添加人员角色关系失败";
    const EDIT_USER_FAIL = "编辑人员失败";
    const EDIT_USER_EXIST = "编辑人员不存在";
    const DEL_USER_FAIL = "删除人员失败";
    const DEL_USER_ROLE_FILE = "删除人员角色关系失败";
    const ADD_GROUP_FILE = "添加组失败";
    const EDIT_GROUP_FILE = "编辑组失败";
    const DEL_GROUP_FILE = "删除组失败";
    const ADD_GROUP_ONLY = "组名已存在，请更换组名";
    const EDIT_GROUP_EXIT = "该组不存在";
    const EDIT_GROUP_USER_EXIT = "该组存在人员，请先转移组员";
    const EDIT_GROUP_CHILD_EXIT = "该组存在子组，请先转移子组";
    const MOVE_GROUP_EXIT = "转移组不存在";
    const MOVE_TO_GROUP_EXIT = "转移目标组不存在";
    const MOVE_TO_GROUP_DIFF = "组的类型不同";
    const MOVE_GROUP_CHILD_FAIL = "转移子组失败";
    const MOVE_GROUP_USER_FAIL = "转移人员失败";
    const MOVE_GROUP_ABLE = "可转移的组为空";
    const CHANGE_USER_ROLE = "升级为组管理员失败";
    const CHANGE_USER_FREE = "更改状态失败";
    const USER_NO_FIND = "用户名不存在";
    const USER_PASS_WRONG = "用户密码错误";
    const PARAMS_WRONG = "参数错误";
    const CHANGE_PASS_WRONG = "修改密码失败";
    const CODE_WRONG = "密码验证码错误";
    const MOBILE_CHANGE = "用户名或密码错误";
    const QUESTION_NONE = "无题目";
    const ASSIST_ID_NOTNULL = "教辅id不能为空";


    const SYSTEM_NOERROR_CODE  = 0;
    const NEW_AGAIN_WRONG = "新密码与确认新密码不一致";
    const OLD_PASS_WRONG = "原密码错误";
}