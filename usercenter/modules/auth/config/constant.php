<?php
###################CACHE相关#####################
define('ENABLE_KNOBOX_CACHE',true); //启用cache开关
define('ANDROID_ERROR_CACHE',false);
define('ANDROID_ERROR_CACHE_TIME', 300); //cache时间
define('CACHE_TIME', 3600); //cache时间
define('CACHE_ACCESS_TOKEN', 'kbox1qaz2wsx');
define('ANDROID_ERROR_CACHE_TIME_FEED_LIST', 60); //cache时间


/*
################## INTERIM  ##########
define('CACHE_ALL_POWERFUL_CODE','ALL_POWERFUL_CODE');//万能校验码
define('CACHE_CITY_SCHOOL',  '1008_'); //城市下的学校
define('CACHE_RELATE_QUESTIOINID',  '1007_'); //题目id列表
define('CACHE_TA_COURSESECTION',  '1006_'); //教辅下的章节信息

define('CACHE_PASSWORD_CODE_TEACHER',  '1009_'); //老師密码校验码
define('CACHE_REGISTER_CODE_TEACHER',  '1010_'); //注册校验码
define('CACHE_REGISTER_CODE_STUDENT',  '1011_'); //注册校验码
define('CACHE_PASSWORD_CODE_STUDENT',  '1012_'); //學生密码校验码
define('CACHE_PROP_CODE_TEACHER',  '1013_'); //老師密码校验码


define('CACHE_TA_GLOBAL',  '1003_'); //学科学段的教辅信息
define('CACHE_TBTA',  '1005_'); //课本下的教辅信息
define('CACHE_TMTA',  '1004_'); //教材下的教辅信息
define('CACHE_TMTBTA_GLOBAL',  '1002_'); //教材课本教辅信息
define('CACHE_STUDENT_VIEWED_HOMEWORKIDS',  '1014_'); //学生查看过的作业id列表

define('ANDROID_ERROR_CACHE_1','3001_');
define('ANDROID_ERROR_CACHE_2','3002_');
define('ANDROID_ERROR_CACHE_3','3003_');
define('ANDROID_ERROR_CACHE_4','3004_');
define('ANDROID_ERROR_CACHE_5','3005_');

define('CACHE_VIRTUAL_TEACHER','1015_');//虚拟老师cache前缀
define('CACHE_HUANXIN_DELETE_CLASS_STUDENT_RELATE',  'HUANXIN_DELETE_CLASS_STUDENT_RELATE'); //班级与学生的关系未删除的数据
define('CACHE_PAPER_FILTERS' ,'1029_');//推荐试卷cache前缀
define('CACHE_KNOWLEDGE_LIST_LEVEL_ONE', '1016_'); //一级知识点列表
define('CACHE_KNOWLEDGE_LIST', '1001_'); //知识点列表
define('CACHE_KNOWLEDGE_LIST_BY_KNOW_ID', '1017_'); //该KnowID下知识点列表
define('CACHE_TA_COURSESECTION_BY_COURSESECTION_ID',  '1018_'); //该id下章节列表
define('CACHE_ISSUE_KNOWLEDGE_LIST',  '1022_'); //题型或知识点
define('CACHE_PAPER_RECOMMEND' ,'1023_');//推荐试卷cache前缀
define('CACHE_TOPIC_ID','1024_');//专题cache前缀
define('CACHE_TOPIC_CURRENT','1025_');//当前专题cache前缀
define('CACHE_TOPIC_NEXT','1026_');//下期专题cache前缀
define('CACHE_TOPIC_ALL_PUBLISHED','1027_');//所有已发布专题cache前缀
define('CACHE_TOPIC_ID_QUESTIONS','1028_');//某专题下的题目详情
define('CACHE_PROP_9_COUNT_DAY' ,'1030_');//当天奖品数
define('CACHE_TIKUMSG' ,'1031_');//题库显示信息
# 新试卷
define('CACHE_PAPER_FILTER_CITY','paper_filter_');
define('CACHE_TEACHER_LOTTERY_RECORD',  'CACHE_TEACHER_LOTTERY_RECORD'); //老师抽奖中奖记录
define('CACHE_STUDENT_LOTTERY_RECORD',  'CACHE_STUDENT_LOTTERY_RECORD'); //老师抽奖中奖记录
#接口访问频率限制
define('CACHE_FILTER_SPAM', '2000_');
define('CACHE_CORRECTING_HOMEWORK_EXPIR', 60 * 60 * 10);//待批改作业缓存时间
define('CACHE_CLASS_CORRECTING_HOMEWORK', 'CACHE_CLASS_CORRECTING_HOMEWORK_%s');

define('HOT_PRE', 'HWCOUNT_');
### ??? never be used
define('COL_PRE', 'PERSONALCOUNT_');
define('CACHE_ISSUE_LIST',  '1019_'); //该知识点题型列表
define('CACHE_ISSUE_LIST_LEVEL_ONE',  '1020_'); //一级题型列表
define('CACHE_ISSUE_LIST_BY_ISSUE_ID',  '1021_'); //该IssueID下题型列表
################## INTERIM  ##########
*/

###################错误码配置#####################
//操作成功
// Modified by zhaoyuankui, 2016/03/29.
// The old TRANSCATION_SUCCESS has spelling error.
define('TRANSCATION_SUCCESS',0);
define('TRANSACTION_SUCCESS',99999);
//服务器内部错误
define('ERRNO_SERVER_INTERNAL_ERROR', 30000);
define('ERROR_SERVER_INTERNAL_ERROR', "INTERNAL SERVER ERROR");

//参数错误
define('ERRNO_PARAM_CHECK_FAILED', 30001);
define('ERROR_PARAM_CHECK_FAILED', '参数错误');
//post参数错误
define('ERRNO_POST_PARAM_CHECK_FAILED', 30002);
define('ERROR_POST_PARAM_CHECK_FAILED', 'post param check failed');
//token失效
define('ERRNO_TOKEN_ERROR', 20001);
define('ERROR_TOKEN_ERROR', 'token error');
//token格式错误
define('ERRNO_TOKEN_FORMAT_INVALID', 30003);
define('ERROR_TOKEN_FORMAT_INVALID', 'token format invalid');
//老师信息不存在
define('ERRNO_FIND_NO_TEACHER', 30004);
define('ERROR_FIND_NO_TEACHER', 'find no teacher');
//密码校验失败
define('ERRNO_CHECK_PASSWD_FAILED', 30005);
define('ERROR_CHECK_PASSWD_FAILED', 'check passwd failed');
//非授权老师(试图操作其他老师的分组等)
define('ERRNO_TEACHER_PERMISSION_DENY', 30006);
define('ERROR_TEACHER_PERMISSION_DENY', 'teacher permission deny');
//老师无作业权限
define('ERRNO_TEACHER_PERMISSION_DENY_OF_HOMEWORK', 30007);
define('ERROR_TEACHER_PERMISSION_DENY_OF_HOMEWORK', 'teacher permission deny of homework');
//老师无班级权限
define('ERRNO_TEACHER_PERMISSION_DENY_OF_CLASS', 30008);
define('ERROR_TEACHER_PERMISSION_DENY_OF_CLASS', 'teacher permission deny of class');
//参数检验失败
define('ERRNO_PARAM_VERIFY_FAIL', 30009);
define('ERROR_PARAM_VERIFY_FAIL', 'param verify fail');
//老师已赞过作业
define('ERRNO_ALREADY_PRAISED', 30010);
define('ERROR_ALREADY_PRAISED', 'already praised homework');
//老师无权分享作业
define('ERRNO_TEACHER_PERMISSION_DENY_OF_SHAREHOMEWORK', 30012);
define('ERROR_TEACHER_PERMISSION_DENY_OF_SHAREHOMEWORK', 'teacher permission deny of share homework');
//生成分享题目链接失败
define('ERRNO_SHAREURL_GENERATE_FAIL', 30013);
define('ERROR_SHAREURL_GENERATE_FAIL', 'shareurl generate fail');
//分享题目链接失效
define('ERRNO_SHAREURL_OUT_OF_DATE', 30014);
define('ERROR_SHAREURL_OUT_OF_DATE', 'shareurl out of date');
//老师无权查看分享内容
define('ERRNO_TEACHER_PERMISSION_DENY_OF_VIEW_SHARE_CONTENT', 30015);
define('ERROR_TEACHER_PERMISSION_DENY_OF_VIEW_SHARE_CONTENT', '不是同科内容暂不能查看');
//老师无权分享分组
define('ERRNO_TEACHER_PERMISSION_DENY_OF_SHAREGROUP', 30016);
define('ERROR_TEACHER_PERMISSION_DENY_OF_SHAREGROUP', 'teacher permission deny of sharegroup');
//老师无权分享专题
define('ERRNO_TEACHER_PERMISSION_DENY_OF_SHARETOPIC', 30017);
define('ERROR_TEACHER_PERMISSION_DENY_OF_SHARETOPIC', 'teacher permission deny of sharetopic');
//老师无权分享题包
define('ERRNO_TEACHER_PERMISSION_DENY_OF_SHAREPACKAGE', 30018);
define('ERROR_TEACHER_PERMISSION_DENY_OF_SHAREPACKAGE', 'teacher permission deny of share package');
//闯关分享域名
define('HURDLE_SHARE_URL','http://api.knowbox.cn/levelShare-online/emigrateResult.html');
//班级无学生
define('ERRNO_EMPTY_CLASS', 30019);
define('ERROR_EMPTY_CLASS', 'empty class');
//非正式用户防抓取.
define('ERRNO_TEACHER_NO_LOGIN',30020);
define('ERROR_TEACHER_NO_LOGIN','查看更多题目请登录并完善个人信息');

//获取默认分组失败
define('ERRNO_GET_DEFAULT_GROUP_FAILED', 31002);
define('ERROR_GET_DEFAULT_GROUP_FAILED', 'get default group failed');
//分组不存在为空
define('ERRNO_FIND_NO_GROUP', 31003);
define('ERROR_FIND_NO_GROUP', '分组不存在');
//存在同名分组
define('ERRNO_DUPLICATE_GROUP_NAME', 31004);
define('ERROR_DUPLICATE_GROUP_NAME', 'duplicate group name');
//题目Answer不存在
define('ERRNO_FIND_NO_ANSWER', 31005);
define('ERROR_FIND_NO_ANSWER', 'find no answer');
//作业不存在
define('ERRNO_FIND_NO_HOMEWORK', 31006);
define('ERROR_FIND_NO_HOMEWORK', 'find no homework');
//作业截止时间小于布置时间
define('ERRNO_ENDTIME_LT_ADDTIME', 31007);
define('ERROR_ENDTIME_LT_ADDTIME', 'endtime less than addtime');
//作业下无题目
define('ERRNO_NO_QUESTION_OF_HOMEWORK', 31008);
define('ERROR_NO_QUESTION_OF_HOMEWORK', 'no question of homework');
//分享题组无题目
define('ERRNO_NO_QUESTION_OF_SHARE', 31009);
define('ERROR_NO_QUESTION_OF_SHARE', 'no question of share');
//单次保存为题组的题目数目超过最大值
define('ERRNO_QUESTION_COUNT_GT_MAX_GROUP',31010);
define('ERROR_QUESTION_COUNT_GT_MAX_GROUP','question count gt max num of group');
//保存题组时未输入题组名字
define('ERRNO_NOT_INPUT_GROUP_NAME',31011);
define('ERROR_NOT_INPUT_GROUP_NAME','not input group name');
//题目已存在题组内
define('ERRNO_QUESTION_EXISTS_IN_GROUP',31012);
define('ERROR_QUESTION_EXISTS_IN_GROUP','question existed in group');
//学生信息不存在
define('ERRNO_FIND_NO_STUDENT_TOKEN', 32001);
define('ERROR_FIND_NO_STUDENT_TOKEN', 'find no student');
//非授权学生(试图获取其他班级的作业等)
define('ERRNO_STUDENT_PERMISSION_DENY', 32002);
define('ERROR_STUDENT_PERMISSION_DENY', 'student permission deny');

//不存在此学科的专题
define('ERRNO_NO_SUBJECT_OF_TOPIC',33001);
define('ERROR_NO_SUBJECT_OF_TOPIC','no subject of topic');

//闯关关卡列表为空
define('ERRNO_EMPTY_HURDLE_LIST',34001);
define('ERROR_EMPTY_HURDLE_LIST','empty hurdle list');
//pk对象的闯关记录不存在
define('ERRNO_EMPTY_PK_HURDLE_RECORD',34002);
define('ERROR_EMPTY_PK_HURDLE_RECORD','empty pk hurdle list');
//体力不足
define('ERRNO_NOT_ENOUGH_ENERGY',34003);
define('ERROR_NOT_ENOUGH_ENERGY','not enough energy');
//PK对象被锁定
define('ERRNO_PK_STUDENTID_LOCKED',34004);
define('ERROR_PK_STUDENTID_LOCKED','pk student locked');
// No school set.
define('ERRNO_SCHOOL_NOT_SET',34005);
define('ERROR_SCHOOL_NOT_SET','school not set');
// Not add to any class.
define('ERRNO_CLASS_NOT_ADD',34006);
define('ERROR_CLASS_NOT_ADD','no class added');
// 没有金币可领取
define('ERRNO_NO_COIN_RECEIVE',35001);
define('ERROR_NO_COIN_RECEIVE','no coin to receive');
// Receive task repeatedly.
define('ERRNO_RECEIVE_TASK_REPEATEDLY',35002);
define('ERROR_RECEIVE_TASK_REPEATEDLY','receive task repeatedly');
// Receive task not exist.
define('ERRNO_TASK_NOT_EXIST',35003);
define('ERROR_TASK_NOT_EXIST','receive task not exist');

//今日赠送体力次数已用完
define('ERRNO_TODAY_SENDTIMES', 37001);
define('ERROR_TODAY_SENDTIMES', 'gift times already exhanted');
//不能赠送对方
define('ERRNO_GIFT_NOT_ALLOW', 37002);
define('ERROR_GIFT_NOT_ALLOW', 'gitf not allow');
//没有可领取的体力
define('ERRNO_NO_ENERGY_RECEIVE', 37003);
define('ERROR_NO_ENERGY_RECEIVE', 'no energy to receive');
//关卡不存在
define('ERRNO_HURDLE_NOT_EXISTS', 37004);
define('ERROR_HURDLE_NOT_EXISTS', 'hurdle not exists');
//pk成功告诉同学失败
define('ERRNO_PUSH_MESSAGE_FAILED', 37005);
define('ERROR_PUSH_MESSAGE_FAILED', 'push message failed');

//金币不足不能被偷取
define('ERRNO_COIN_NOT_ENOUGH', 38001);
define('ERROR_COIN_NOT_ENOUGH', 'coin not enough');
//今日不可再pk
define('ERRNO_CAN_NOT_PK', 38002);
define('ERROR_CAN_NOT_PK', 'can not pk');
//今日不可被再pk
define('ERRNO_CAN_NOT_BE_PK', 38003);
define('ERROR_CAN_NOT_BE_PK', 'can not be pk');
//对手无闯关记录
define('ERRNO_NO_HURDLE_RECORD', 38004);
define('ERROR_NO_HURDLE_RECORD', 'no hurdle record');
//pk关卡每日限制次数
define('ERRNO_PKWIN_PER_HURDLE', 38005);
define('ERROR_PKWIN_PER_HURDLE', 'pk not allowed');
//偷取金币放弃时pk记录不存在
define('ERRNO_PKRECORD_NOT_EXISTS', 38006);
define('ERROR_PKRECORD_NOT_EXISTS', 'no pk record');


//--------老师给学生送金币----------
//金币赠送完毕
define('ERRNO_COIN_NO_EMAINED', 37006);
define('ERROR_COIN_NO_EMAINED', 'no coin remained');
//为完成任务
define('ERRNO_COIN_NO_FINISH_TASK', 37007);
define('ERROR_COIN_NO_FINISH_TASK', 'task not finished');
//金币重复赠送
define('ERRNO_COIN_DUPLICATE_GRANT', 37008);
define('ERROR_COIN_DUPLICATE_GRANT', 'coin duplicate grant');

// 偷金币护盾相关状态码
define('ERRNO_BUFF_CLOSED', 50001);
define('ERROR_BUFF_CLOSED', 'buff already closed');
define('ERRNO_BUFF_OPENED', 50002);
define('ERROR_BUFF_OPENED', 'buff already opened');
define('ERRNO_BUFF_CAN_NOT_OPEN', 50003);
define('ERROR_BUFF_CAN_NOT_OPEN', 'can not open buff');
define('ERRNO_BUFF_OPEN_CAN_NOT_STEAL', 50004);
define('ERROR_BUFF_OPEN_CAN_NOT_STEAL', 'buff open can not steal');
define('ERRNO_SELF_BUFF_OPENED', 50005);
define('ERROR_SELF_BUFF_OPENED', 'self buff opend can not steal');

//金币商城相关状态码
define('ERRNO_STUDENT_LEVEL_NOT_ENOUGH',36001);
define('ERRNO_STOCK_LESS',36002);
define('ERRNO_SUBMIT_ADDRESS_FAIL',36003);
define('ERROR_SUBMIT_ADDRESS_FAIL','submit address fail');
define('ERRNO_COMMODITY_UNFIND',36004);
define('ERROR_COMMODITY_UNFIND','commodity unfind');
define('MALL_QQB_GOODID',702349);
define('MALL_QQVIP_GOODID',921299);
define('ERRNO_NO_THIS_GOOD',36006);
define('ERROR_NO_THIS_GOOD','good no exist');
define('ERRNO_MISS_NECESSARY_PARAMETER',36007);
define('ERROR_MISS_NECESSARY_PARAMETER','缺少必要参数');
define('ERRNO_NO_THIS_RECORD',36008);
define('ERROR_NO_THIS_RECORD','record no exist');

//数据库写入失败
define('ERRNO_DB_SAVE_FAIL', 40000);
define('ERROR_DB_SAVE_FAIL', '密码修改失败');

//lan 老接口迁移错误码
//题目不存在
define('ERRNO_QUESTION_IS_NULL', 20006);
define('ERROR_QUESTION_IS_NULL', 'question is null');
//数据没有修改
define('ERRNO_DATA_NOT_MODIFY',20018);
define('ERROR_DATA_NOT_MODIFY','data not modify');
//班级不存在
define('ERRNO_FIND_NO_CLASS',20303);
define('ERROR_FIND_NO_CLASS','class no find');
//班级已关闭
define('ERRNO_CLOSED_CLASS',20019);
define('ERROR_CLOSED_CLASS','class is closed');
//重复加入班级
define('ERRNO_DUPLICATE_JOIN_CLASS',20020);
define('ERROR_DUPLICATE_JOIN_CLASS','duplicate join class');
//转移老师不存在
define('ERRNO_FIND_NO_TRANSFER_TEACHER',20016);
define('ERROR_FIND_NO_TRANSFER_TEACHER','transfer teacher no find');
//转移老师学科不匹配
define('ERRNO_TRANSFER_TEACHER_SUBJECT_ERROR',20017);
define('ERROR_TRANSFER_TEACHER_SUBJECT_ERROR','transfer teacher subject error');
//密码错误
define('ERRNO_PASSWORD_ERROR',20205);
define('ERROR_PASSWORD_ERROR','密码错误');
//密码错误
define('ERRNO_OLD_PASSWORD_ERROR',20206);
define('ERROR_OLD_PASSWORD_ERROR','old password error');
//手机号存在
define('ERRNO_MOBILE_EXIST',20501);
define('ERROR_MOBILE_EXIST','mobile exist');
define('ERRNO_MOBILE_NO_EXIST',20505);
define('ERROR_MOBILE_NO_EXIST','mobile no exist');
//学生不存在
define('ERRNO_STUDENT_NO_EXIST',20013);
define('ERROR_STUDENT_NO_EXIST','student no exist');
//五分钟之内不能催作业
define('ERRNO_SEND_REMIND_MESSAGE_TOO_FREQUENTLY', 21208);
define('ERROR_SEND_REMIND_MESSAGE_TOO_FREQUENTLY', 'send remind message too frequently');
//帐号错误
define('ERRNO_FIND_NO_LOGIN_NAME', 20204);
define('ERROR_FIND_NO_LOGIN_NAME', '电话号码不存在');
//学生不存在
define('ERRNO_FIND_NO_STUDENT', 20901);
define('ERROR_FIND_NO_STUDENT', '用户不存在');
//校验码过期
define('ERRNO_PASSWORD_CODE_EXPIRE', 20507);
define('ERROR_PASSWORD_CODE_EXPIRE', 'password_code_expire');

//忘記密碼驗證碼错误
define('ERRNO_PASSWORD_CODE_ERROR', 20506);
define('ERROR_PASSWORD_CODE_ERROR', 'password_code_error');
//注册校验码错误
define('ERRNO_REGISTER_CODE_ERROR', 20508);
define('ERROR_REGISTER_CODE_ERROR', 'register_code_error');
//体验账号人数过多
define('ERRNO_EXPERIENCE_ACCOUNT_TOO_MANY', 20509);
define('ERROR_EXPERIENCE_ACCOUNT_TOO_MANY', 'experience_account_too_many');
//自定义错误
define('ERRNO_CUSTOM_MSG', 20014);
define('ERROR_CUSTOM_MSG', 'custom_message');
define('ERROR_TOO_MANY_QUESTIONS', '无法布置作业。作业一次最多只能布置50道题，请去掉部分题目');
//老师删除作业
define('ERRNO_HOMEWORK_TEACHER_DELETE', 20601);
define('ERROR_HOMEWORK_TEACHER_DELETE', 'teacher delete homework');
//不能yo自己
define('ERRNO_CAN_NOT_YO_SELF', 20403);
define('ERROR_CAN_NOT_YO_SELF', 'can not yo self');
//yo过了
define('ERRNO_YO_MORE', 20402);
define('ERROR_YO_MORE', 'yo two times');
//微信分享账号类型非法
define('ERRNO_WECHAT_ACCOUNT_TYPE_ILLEAGE', 20404);
define('ERROR_WECHAT_ACCOUNT_TYPE_ILLEAGE', '微信分享账号类型非法');


//反抓取频率限制提示
define('ERROR_CUSTOM_TOO_FREQUENT_MSG', '您的操作频率过快,请稍后再试');

//单个班级学生人数上限
define('MAX_STUDENT_NUM_PER_CLASS', 200);

//每次保存为题组的题目总数最大值
define('MAX_COUNT_QUESTION_TO_GROUP',200);

//虚拟老师密码
define('PASSWORD_VIRTUAL_TEACHER','123456');

//万能密码
define('PASSWORD_ALL_POWERFUL','100030417');
define('VERIFY_CODE_KEY','lanjj@knwobox.comlanjunjia4@qq.com232713267@qq.com');

//题目来源
define('QS_COURSESECTION', 'coursesection');
define('QS_KNOWLEDGE', 'knowledge');
define('QS_PAPER', 'paper');
define('QS_PERSONAL_GROUP', 'personalgroup');
define('QS_PHOTO_QUESTION', 'photoquestion');

//题组类型
define('QG_MYCOLLECT', 1);
define('QG_PHTOTOGROUP', 2);
define('QG_FROMSHARE', 3);
define('QG_FROMTOPIC',4);//题组来源于专题

//年级
define('GRADE_FIRST_PRIMARY', 1);
define('GRADE_SECOND_PRIMARY', 2);
define('GRADE_THIRD_PRIMARY', 3);
define('GRADE_FOURTH_PRIMARY', 4);
define('GRADE_FIFTH_PRIMARY', 5);
define('GRADE_SIXTH_PRIMARY', 6);
define('GRADE_FIRST_MIDDLE',11);
define('GRADE_SECOND_MIDDLE', 12);
define('GRADE_THIRD_MIDDLE', 13);
define('GRADE_FOURTH_MIDDLE',14);
define('GRADE_FIRST_HIGH',21);
define('GRADE_SECOND_HIGH', 22);
define('GRADE_THIRD_HIGH', 23);


//题型
define('QUESTION_TYPE_CHOICE',0);
define('QUESTION_TYPE_MUTICHOICE', 1);
define('QUESTION_TYPE_ANSWER', 2);
define('QUESTION_TYPE_FILLIN',3);
define('QUESTION_TYPE_TRANSLATE',4);
define('QUESTION_TYPE_CLOZE', 5);
define('QUESTION_TYPE_COMPREHENSION', 6);
define('QUESTION_TYPE_RATIONAL',7);
define('QUESTION_TYPE_COMPOSITION', 8);
define('QUESTION_TYPE_ANSWERFILLIN',9);
define('QUESTION_TYPE_HEARINGSHORT',11);
define('QUESTION_TYPE_HEARINGLONG',12);
define('QUESTION_TYPE_HEARINGDUBAI',13);

//题目得分
define('QUESTION_SCORE_UNTOUCH', -1);
define('QUESTION_SCORE_WRONG', 0);
define('QUESTION_SCORE_HALFRIGHT', 1);
define('QUESTION_SCORE_RIGHT', 2);

define('CHAT_USER_TYPE_PUBLIC_ACCOUNT',0);
define('CHAT_USER_TYPE_TEACHER',1);
define('CHAT_USER_TYPE_STUDENT',2);
define('CHAT_USER_TYPE_SYSTEM',3);
define('CHAT_USER_TYPE_GROUP',4);

#分享item类型
define('SHARE_HOMEWORK', 1);
define('SHARE_QUESTIONGROUP', 2);
define('SHARE_TOPIC', 3);
define('SHARE_QUESTIONPACKAGE', 4);

define('WEB_SERVER_IMAGE_PATH','http://file.knowbox.cn/upload/question/');
define('WEB_SERVER_HEADPHOTO_PATH','http://file.knowbox.cn/upload/headPhoto/');
define('HEADPHOTO_DEFAULT_BOY','http://7xjnvd.com2.z0.glb.qiniucdn.com/default_photo.png');
define('HEADPHOTO_DEFAULT_GIRL','http://7xjnvd.com2.z0.glb.qiniucdn.com/default_photo.png');

define('SMS_MESSAGE_REGISTER','【作业盒子】验证码：{0}。如非本人操作，请忽略。');
define('SMS_MESSAGE_FORGET_PASSWORD','【作业盒子】 找回密码验证码：{0} 。如非本人操作，请忽略。');
define('SMS_MESSAGE_INVITATION','【作业盒子】我是{0}老师，正在使用作业盒子给学生轻松布置作业，无需费力批改。现加入并输入邀请码{1}，可获得XX元话费。点击下载：www.zuoyehezi.com');
define('SMS_MESSAGE_TRANSFER_CLASS','【作业盒子】我是{0}老师，我把作业盒子里的班级转交给你现在加入并输入邀请码{1}，可获得30元话费。点击下载：www.zuoyehezi.com');
define('SMS_MESSAGE_VERIFY_PROP','【作业盒子】 邀请验证码：{0} 。如非本人操作，请忽略。');

define('SMS_MESSAGE_PICA_REGISTER','校验码 ：{0}。如非本人操作，请忽略。');
define('SMS_MESSAGE_PICA_FORGET_PASSWORD','找回密码校验码 ：{0} 。如非本人操作，请忽略。');
define('SMS_MESSAGE_PICA_INVITATION','我是{0}老师，正在使用作业盒子给学生轻松布置作业，无需费力批改。现加入并输入邀请码{1}，可获得XX元话费。点击下载：www.zuoyehezi.com');
define('SMS_MESSAGE_PICA_TRANSFER_CLASS','我是{0}老师，我把作业盒子里的班级转交给你现在加入并输入邀请码{1}，可获得30元话费。点击下载：www.zuoyehezi.com');
define('SMS_MESSAGE_PICA_VERIFY_PROP',' 邀请检验码：{0} 。如非本人操作，请忽略。');

define('SMS_MESSAGE_REGISTER_SUCCESS','老师您好，欢迎加入作业盒子~为了让您更好地使用盒子，推荐下载学生端体验完整的作业流程~下载地址：hzapp.knowbox.cn');
define('SMS_MESSAGE_REGISTER_SUCCESS_WITH_PASSWORD_1','老师您好，欢迎加入作业盒子~ 我们为您分配密码为:');
define('SMS_MESSAGE_REGISTER_SUCCESS_WITH_PASSWORD_2',' 以方便您登陆作业盒子主页。您可以在个人设置中修改此密码。为了让您更好地使用盒子，推荐下载学生端体验完整的作业流程~下载地址：hzapp.knowbox.cn');

/**
 * @deprected
 * @alsosee common\moduele\WxEduInvitelog
 */
define('STUDENT_INVITE_H5',30201);
define('TEACHER_INVITE_H5',30101);
define('TEACHER_INVITE_AUTH',30102);

define('SMS_ALL_POWERFUL_CODE','2212');

// define('CITY_VERSION','4');
// define('CITY_DATA_ANDROID','http://7xjnvd.com2.z0.glb.qiniucdn.com/CityList-20160512.json');
// define('CITY_DATA_IOS','http://7xjnvd.com2.z0.glb.qiniucdn.com/CityList-20160512.plist');
//define('CITY_VERSION','5');
//define('CITY_DATA_ANDROID','http://7xjnvd.com2.z0.glb.qiniucdn.com/citylist-20160830.json');
//define('CITY_DATA_IOS','http://7xjnvd.com2.z0.glb.qiniucdn.com/citylist-20160830.plist');
define('CITY_VERSION','6');
define('CITY_DATA_ANDROID','http://7xjnvd.com2.z0.glb.qiniucdn.com/20161102citylist.json');
define('CITY_DATA_IOS','http://7xjnvd.com2.z0.glb.qiniucdn.com/20161109citylist.plist');


####################redis外部数据前缀#################
//

#########################其他#########################
/**
 * 七牛云
 * https://portal.qiniu.com
 */
define('QINIU_DOWNLOAD_HOST_TEST', 'http://7xkdpi.com2.z0.glb.qiniucdn.com');
define('QINIU_DOWNLOAD_HOST_KNOWBOX', 'http://7xjnvd.com2.z0.glb.qiniucdn.com');
define('QINIU_DOWNLOAD_HOST_KNOWBOX_AUDIO', 'http://7xjqro.com2.z0.glb.qiniucdn.com');
define('QINIU_DOWNLOAD_HOST_TIKU', 'http://7xohdn.com2.z0.glb.qiniucdn.com');
define('QINIU_ACCESS_KEY', 'nJL7e6J3VAIC5j4DqE-KKUeOv2LkGTaN4YjXBw7F');
define('QINIU_SECRET_KEY', 'n4Yt7k2bNwvA7rS4ETENhl_cGhnsS9hUrGFamwvX');
define('QINIU_BUCKET_TEST', 'knowbox-test');   //测试空间名
define('QINIU_BUCKET_AUDIO', 'knowbox-audio'); //音频空间名
define('QINIU_BUCKET_KNOWBOX', 'knowbox');     //默认空间名
define('QINIU_UPLOAD_TOKEN_VALIDTIME', 3600);  //上传token有效时长

#pica短信服务
define('PICA_URL_SENDSMS', 'http://sms.pica.com/zqhdServer/sendSMS.jsp');
define('PICA_URL_GETBALANCE', 'http://sms.pica.com/zqhdServer/getbalance.jsp');
define('PICA_REGCODE', 'ZXHD-CRM-0100-HIWUAP');
define('PICA_PASSWD', '26aa924f6d1f909d0cfa25a730ff10b8');
define('PICA_HARDKEY', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

#题目css样式(内容发生变化时需升级版本号)
define('TIKU_QUESTION_CSS_URL', QINIU_DOWNLOAD_HOST_TIKU.'/resource/llkt20160831.css');
define('TIKU_QUESTION_JS_URL_v1', QINIU_DOWNLOAD_HOST_TIKU.'/resource/audioControl-20160905.js');
define('TIKU_QUESTION_JS_URL_v2', QINIU_DOWNLOAD_HOST_TIKU.'/resource/audioNative-20160905.js');
define('TIKU_STUDENT_ASSETS_URL', QINIU_DOWNLOAD_HOST_TIKU .'/resource/student-assets-20160803.zip');
define('TIKU_STUDENT_ASSETS_340URL', QINIU_DOWNLOAD_HOST_TIKU .'/resource/student-assets-20160918.zip');

#Hdw短信服务
define('HDW_URL_SENDSMS_URL', 'http://ws.hdwinfo.cn:8080/sdkproxy/sendsms.action');
define('HDW_CDKEY', 'JZK-6364DJBQOM');
define('HDW_PASSWD', '232713267');

#guodu短信服务
define('GUODU_URL_SENDSMS', 'http://221.179.180.158:9007/QxtSms/QxtFirewall');
define('GUODU_URL_GETBALANCE', 'http://221.179.180.158:8081/QxtSms_surplus/surplus');
define('GUODU_OPERID', 'zsyxzy');
define('GUODU_OPERPASS', 'zsyxzy');

#yimei短信服务
define('YIMEI_URL_SENDSMS', 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/sendsms.action');
define('YIMEI_URL_GETBALANCE', 'http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/querybalance.action');
define('YIMEI_CDKEY', '9SDK-EMY-0999-RDUUM');
define('YIMEI_PASSWORD', '816577');

#请求参数加密/解密
define('GET_SALT', '1qaz2wsx');
define('POST_SALT', '123qwe!@#');

#小红花作业标识
define('RED_FLOWER_HOMEWORK_ICON', 'http://7xjnvd.com2.z0.glb.qiniucdn.com/icon_smallflower.png');
define('RED_FLOWER_HOMEWORK_ICON_DARK', 'http://7xjnvd.com2.z0.glb.qiniucdn.com/icon_smallflower_dark2.png');
define('RED_FLOWER_HOMEWORK_ICONDESC1', '小红花!');
define('RED_FLOWER_HOMEWORK_ICONDESC2', '该作业被评为小红花!');
define('RED_FLOWER_HOMEWORK_PRODESC', '话费补贴快来拿');

#学生端小红花活动开关
define('RED_FLOWER_HOMWORK_STUDENT_ACTIVITY_ON', true);
define('RED_FLOWER_HOMEWORK_ICONDESC_STUDENT1', '本次作业按时提交率{SUBMITRATE}%，获得小红花，得分{SCORE}');
define('RED_FLOWER_HOMEWORK_ICONDESC_STUDENT2', '班群再加入{NEEDSTUDENTCOUNT}人可参与活动');
define('RED_FLOWER_HOMEWORK_ICONDESC_STUDENT3', '本次作业按时提交率{SUBMITRATE}%，还差{NEEDSUBMITCOUNT}人就能获得小红花');
define('RED_FLOWER_HOMEWORK_PRODESC_STUDENT', '更多礼品快来拿');

#接口地址
//define('API_DOTNET_TEST_URL_LAN','http://192.168.1.138:98');
define('API_DOTNET_TEST_URL_LAN','http://192.168.0.26:98/code/2.2.2');
define('API_TEST_URL_LAN','http://192.168.1.49:80');

define('API_DOTNET_PRO_URL','http://napi.knowbox.cn/code/2.2.2');
define('API_DOTNET_TEST_URL','http://napi.knowbox.cn/code/test');
define('API_PRO_URL','http://api.knowbox.cn');
define('API_TEST_URL','http://123.59.41.137:8000');
define('API_LAN_URL','http://localhost:82');

#学情分析页面路径
define('XUEQING_PATH', '/knowledgeGraph/assets/dist/release/index.html?');
#开宝箱跳转到奖励列表页面
define('BOX_REWARD_URL', '/icoin-market/chestReward.html?');

#活动覆盖的版本(该版本没有活动，该版本之前才有活动）
define('TEACHER_ACTIVITY_VERSION_OPEN','343');
define('STUDENT_ACTIVITY_VERSION_OPEN','344');
#IOS审核用金币开关
define('STUDENT_GOLD_VERSION_OPEN', '344');
#define('STUDENT_ACTIVITY_VERSION_OPEN','301');
define('ACTIVITY_REDFLOWER_JD_OPEN_TIME','2016-01-25 00:00:00');
define('ACTIVITY_REDFLOWER_JD_CLOSE_TIME','2016-02-29 00:00:00');
define('ACTIVITY_TEACHER_LOTTERY_OPEN_TIME','2016-03-16 00:00:00');
define('ACTIVITY_TEACHER_LOTTERY_CLOSE_TIME','2016-04-07 00:00:00');
define('ACTIVITY_JD_URL','http://api.knowbox.cn');
define('ACTIVITY_BRAIN_OPEN_TIME','2016-05-17 00:00:00');
define('ACTIVITY_BRAIN_CLOSE_TIME','2016-06-06 00:00:00');
define('ACTIVITY_BRAIN_URL','http://dwz.cn/3mMpj2');
#学生端做作业抽奖活动
define('ACTIVITY_HOMEWORK_OPEN_TIME','2016-02-02 00:00:00');
define('ACTIVITY_HOMEWORK_OPEN_CLOSE_TIME','2016-05-01 00:00:00');
define('ACTIVITY_HOMEWORK_URL','http://api.knowbox.cn');
#老师端小红花作业抽奖活动
define('ACTIVITY_TEACHER_HOMEWORK_OPEN_TIME','2016-03-16 00:00:00');
define('ACTIVITY_TEACHER_HOMEWORK_OPEN_CLOSE_TIME','2016-04-13 00:00:00');
define('ACTIVITY_TEACHER_HOMEWORK_URL','http://api.knowbox.cn');
#老师端/学生端新年贺卡活动
define('ACTIVITY_NEWYEAR_CARD_OPEN_TIME','2016-02-01 00:00:00');
define('ACTIVITY_NEWYEAR_CARD_OPEN_CLOSE_TIME','2016-02-15 00:00:00');
define('ACTIVITY_NEWYEAR_CARD_STUDENT_URL','http://t.cn/Rb1Ns0x');
define('ACTIVITY_NEWYEAR_CARD_TEACHER_URL','http://t.cn/RbBHHXi');
define('ACTIVITY_INVITE_CLOSE_TIME','2016-03-28 00:00:00');
#老师端课题申请活动
define('ACTIVITY_TEACHER_KCSQ_OPEN_TIME','2016-05-01 00:00:00');
define('ACTIVITY_TEACHER_KCSQ_CLOSE_TIME','2016-06-01 00:00:00');
#老师端母亲节活动
define('ACTIVITY_TEACHER_MOTHERDAY_OPEN_TIME','2016-05-06 20:00:00');
define('ACTIVITY_TEACHER_MOTHERDAY_CLOSE_TIME','2016-05-09 22:00:00');
#学生端胜寒活动
define('ACTIVITY_STUDENT_SHENGHAN_OPEN_TIME', '2016-07-07 10:00:00');
#周末金币翻倍活动
define('ACTIVITY_STUDENT_DOUBLECOIN_OPEN_TIME', '2016-07-22 00:00:00');
define('ACTIVITY_STUDENT_DOUBLECOIN_CLOSE_TIME', '2016-08-31 00:00:00');

//教师认证
define('TEACHER_CERTIFICATE_RULE','认证规则:<br>1.至少一个班群学生数≥20人，且布置过作业；<br>2.认证仅针对初、高中语文、数学、英语、物理、化学、生物、政治、历史、地理、信息技术老师');

// Add by zhaoyuankui, 2016/04/09.
// Default log tag.
define('DEFAULT_LOG_TAG', 'knowbox');

# Shopping center version.
define('SHOPPING_CENTER_VERSION_OPEN','310');
# Friends invite version.
define('FRIENDS_INVITE_VERSION_OPEN','310');

################题组类型相关配置###################
define('BASIC_TYPE_ASSIST', 1);
define('BASIC_TYPE_KNOWLEDGE', 2);
define('BASIC_TYPE_PAPER', 3);
define('BASIC_TYPE_PERSONALGROUP', 4);
define('BASIC_TYPE_PHOTO', 5);
define('BASIC_TYPE_SUPERSEARCH', 6);
define('BASIC_TYPE_SHARE', 7);
define('BASIC_TYPE_SYN', 8);
define('BASIC_TYPE_TOPIC', 9);
define('BASIC_TYPE_ISSUE', 10);
define('BASIC_TYPE_HOMEWORK', 11);

define('SEARCH_TYPE_QUESTION', 21);
define('SEARCH_TYPE_KNOWLEDGE', 22);
define('SEARCH_TYPE_ISSUE', 23);
define('SEARCH_TYPE_PAPER', 24);

/**
 * @deprected
 * @also see common\models\KboxBasePackage
 */
define('GROUP_TYPE_PGC', 31);
define('GROUP_TYPE_KNOWLEDGE', 32);
define('GROUP_TYPE_ISSUE', 33);
define('GROUP_TYPE_SCHOOLPAPER', 34);
define('GROUP_TYPE_GOVPAPER', 35);
//define('AUTO_TYPE_CB', 41); //语基自动出题

############ 默认收藏名字 #####################
define('DEFAULT_GROUP_NAME','收藏的散题');



#####环信推送#####
define('EM_CHAT_TYPENO_SET_HOMEWORK',0);
define('EM_CHAT_TYPECODE_SET_HOMEWORK','set');
define('EM_CHAT_TYPENO_SUBMIT_HOMEWORK',1);
define('EM_CHAT_TYPECODE_SUBMIT_HOMEWORK','submit');
define('EM_CHAT_TYPENO_CLOSE_CLASS',2);
define('EM_CHAT_TYPECODE_CLOSE_CLASS','closeClass');
define('EM_CHAT_TYPENO_JOIN_CLASS',3);
define('EM_CHAT_TYPECODE_JOIN_CLASS','joinClass');
define('EM_CHAT_TYPENO_CORRECT_HOMEWORK',4);
define('EM_CHAT_TYPECODE_CORRECT_HOMEWORK','correct');
define('EM_CHAT_TYPENO_TIP',5);
define('EM_CHAT_TYPECODE_TIP','tip');
define('EM_CHAT_TYPENO_YO',6);
define('EM_CHAT_TYPECODE_YO','yo');
define('EM_CHAT_TYPENO_YO_REPLY',7);
define('EM_CHAT_TYPECODE_YO_REPLY','yoReply');
define('EM_CHAT_TYPENO_PAIR',8);
define('EM_CHAT_TYPECODE_PAIR','pair');
define('EM_CHAT_TYPENO_PRAISE',9);
define('EM_CHAT_TYPECODE_PRAISE','praise');
define('EM_CHAT_TYPENO_RECOMMEND',10);
define('EM_CHAT_TYPECODE_RECOMMEND','recommend');
define('EM_CHAT_TYPENO_REMIND',11);
define('EM_CHAT_TYPECODE_REMIND','remind');
define('EM_CHAT_TYPENO_CHAT_QUESTION',12);
define('EM_CHAT_TYPECODE_CHAT_QUESTION','chatQuestion');
define('EM_CHAT_TYPENO_OUT_CLASS',13);
define('EM_CHAT_TYPECODE_OUT_CLASS','outClass');
define('EM_CHAT_TYPENO_ACTIVITY',14);
define('EM_CHAT_TYPECODE_ACTIVITY','activity');
define('EM_CHAT_TYPENO_CERTIFICATE',15);
define('EM_CHAT_TYPECODE_CERTIFICATE','certificate');
define('EM_CHAT_TYPENO_GROUP_CHAT',16);
define('EM_CHAT_TYPECODE_GROUP_CHAT','groupChat');
define('EM_CHAT_TYPENO_GROUP_SET_HOMEWORK',17);
define('EM_CHAT_TYPECODE_GROUP_SET_HOMEWORK','groupSetHomework');
define('EM_CHAT_TYPENO_SHARE_QUESTIONS',18);
define('EM_CHAT_TYPECODE_SHARE_QUESTIONS','shareQuestions');
define('EM_CHAT_TYPENO_CRON_HOMEWORK',19);
define('EM_CHAT_TYPECODE_CRON_HOMEWORK','crontabHomework');
define('EM_CHAT_TYPENO_TEACHER_CLASSDAILYRANK_UPDATE', 21);
define('EM_CHAT_TYPENO_STUDENT_CLASSDAILYRANK_UPDATE', 24);
define('EM_CHAT_TYPENO_T2S_PRAISE_COMPLETE', 22);
define('EM_CHAT_TYPENO_S2S_PRAISE_COMPLETE', 23);
define('EM_CHAT_TYPENO_PK_WIN', 25);
define('EM_CHAT_TYPENO_SCORE_OVER', 26);
define('EM_CHAT_TYPENO_TASK_COMPLETE', 27);
define('EM_CHAT_TYPENO_ENERGY_SEND', 28);
define('EM_CHAT_TYPENO_STEAL_WIN', 29);
define('EM_CHAT_TYPENO_STEAL_WIN_CLASS', 30);
define('EM_CHAT_TYPENO_TASK_MARK', 31);
define('EM_CHAT_TYPENO_SEND_PRODUCT', 32);
define('EM_CHAT_TYPENO_SKILL_LEVEL_UP', 33);
define('EM_CHAT_TYPENO_BOX_UNRECEIVE', 34);


//推送用户类型
define('PUSH_USER_TYPENO_ALL_USER',0);
define('PUSH_USER_TYPECODE_ALL_USER','AllUser');
define('PUSH_USER_TYPENO_ALL_TEACHER',1);
define('PUSH_USER_TYPECODE_ALL_TEACHER','AllTeacher');
define('PUSH_USER_TYPENO_ALL_STUDENT',2);
define('PUSH_USER_TYPECODE_ALL_STUDENT','AllStudent');
define('PUSH_USER_TYPENO_SINGLE_TEACHER',3);
define('PUSH_USER_TYPECODE_SINGLE_TEACHER','SingleTeacher');
define('PUSH_USER_TYPENO_SINGLE_STUDENT',4);
define('PUSH_USER_TYPECODE_SINGLE_STUDENT','SingleStudent');
define('PUSH_USER_TYPENO_CLASS_STUDENT',5);
define('PUSH_USER_TYPECODE_CLASS_STUDENT','ClassStudent');
define('PUSH_USER_TYPENO_GROUP',6);
define('PUSH_USER_TYPECODE_GROUP','Group');
//define('EM_CHAT_TYPENO_STEAL_WIN', 29);
//define('EM_CHAT_TYPENO_STEAL_WIN_CLASS', 30);


//推送用户类型
define('INFO_TYPENO_TRANSFER_CLASS',0);
define('INFO_TYPENO_REJECT_TRANSFER_CLASS',1);
define('INFO_TYPENO_SUCCESS_TRANSFER_CLASS',2);
define('INFO_TYPENO_STUDENT_KNOW_TRANSFER_CLASS',3);
define('INFO_TYPENO_ADVERTISEMENT',4);

//推送的发起者
define('EM_CHAT_EM_SYS',0); //系统
define('EM_CHAT_EM_TEACHER',1); //老师
define('EM_CHAT_EM_GOODHOMEWORK',2); //优秀作业
define('EM_CHAT_EM_ZUOYEHEZI',3); //作业盒子
define('EM_CHAT_NM_ZUOYEHEZI', '作业盒子');
define('EM_CHAT_PH_ZUOYEHEZI', QINIU_DOWNLOAD_HOST_KNOWBOX .'/activity.png');
define('EM_CHAT_EM_SHAREHOMEWORK',4); //分享作业
define('EM_CHAT_EM_APNS_EXT',5); //apns通道
define('EM_CHAT_EM_AUTO_EXCERSIE',6); //自主练习
define('EM_CHAT_NM_AUTO_EXCERSIE', '每日积分榜');
define('EM_CHAT_PH_AUTO_EXCERSIE', QINIU_DOWNLOAD_HOST_KNOWBOX .'/autoexcersise.png');


//老师认证奖励
define('TEACHER_CERT_STEP4_PROP',18);//步骤4奖励50
define('TEACHER_CERT_STEP5_PROP',19);//步骤5奖励30
//教师认证邀请奖励
define('TEACHER_INVITE_STEP4_PROP',20);//邀请奖励
//小红花奖励
define('TEACHER_RED_FLOWER_PROP',21);//小红花奖励



//引导关闭次数限制
define('GLOBAL_INDEX_CLOSE_LIMIT',3);
//引导状态
define('GLOBAL_INDEX_VIRTUAL',1);//虚拟作业引导
define('GLOBAL_INDEX_CERT',2);//教师认证

//开关
define('TEACHER_SWITCH',1);//教师端引导下载学生端的开关 1开 0关

//领奖类型
define('PRIZE_TYPE_TEACHER_NEW_TASK',1);//新手任务
define('PRIZE_TYPE_TEACHER_INVITE',2);//新手任务

//活动列表
define('ACTIVITY_NEW_TASK',4);
define('ACTIVITY_RED_FLOWER',5);

//跨域测试开关
define('CROSS_HEAD', true);
//define('CROSS_HEAD', false);

//PK活动
define('PKACTIVITY_ENGLISH_PK_TYPE',1);
define('PKACTIVITY_ENGLISH_PK_FOR_ZB_TYPE',2);
define('PKACTIVITY_ENGLISH_PK_ZB_ALL',3);
define('PKACTIVITY_MATH_BEIJING',4);

//海边网活动相关
define('ERRNO_NO_KNOWLEDGE_HURDLE',36601);
define('ERROR_NO_KNOWLEDGE_HURDLE','无做题记录');
define('ERRNO_NO_KNOWLEDGE_HURDLE_WRONG',36602);
define('ERROR_NO_KNOWLEDGE_HURDLE_WRONG','无错题记录');
define('PKACTIVITY_ENGLISH_PK_ZB_ALL_2',5);

//试卷类型组
define('PAPER_TYPE_GROUP_SYN',1);
define('PAPER_TYPE_GROUP_PRO',2);
define('WX_AUTH_PASS','qwertyuiop123');

//关卡活动
define('HURDLE_ACTIVITY_ANIME_CULTURE_TYPE','21');//二次元的终极挑战 类型
define('HURDLE_ACTIVITY_ANIME_CULTURE_TYPE_PARENT','20');//二次元的终极挑战 类型

define('HURDLE_ACTIVITY_ANIME_CULTURE_MSG',
<<<ABC
<span>获得</span>
<span class="red-strong">"{title}"</span>
<span>称号</span>
###
<span>{name}在【作业盒子】</span>
###
<span>"二次元之终极挑战"中累计答对</span>
<span class="theme-strong">{count}</span>
<span>道题目</span>
###
<span>击败了</span>
<span class="theme-strong">{rank}%</span>
<span>的二次元骚年</span>
ABC
);

define('HURDLE_ACTIVITY_ANIME_CULTURE_MSG2',
<<<ABC
<span class="red-strong">三次元无疑</span>
###
<span>{name}在【作业盒子】</span>
###
<span>"二次元之终极挑战"中累计答对</span>
<span class="theme-strong">{count}</span>
<span>道题目</span>
###
<span>滚回你的三次元吧<span>
ABC
);

//default pagelimit 从1 开始
define('DEFAULT_PAGE_LIMIT',5);

//单节点下最多题目数量
define('MAX_QUESTION_COUNT',1000);


//define('DAXIANG_HOST', 'http://daxiangshuju.com/');
define('DAXIANG_HOST', 'http://www.daxiangshuju.com/');
//define('DAXIANG_HOST', 'http://192.168.1.49:5900/');
//define('DAXIANG_HOST', 'http://192.168.1.45:5900/');
define('DAXIANG_SALT', 'I love you');
