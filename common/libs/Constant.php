<?php
namespace common\libs;


class Constant{

    const SWITCH_CACHE = 1;

    const LOGIN_TOKEN_NAME = "IUCTOKEN";

    ########  产品  ###
    const PRODUCT_KBOX = 1;
    const PRODUCT_SUSUAN = 2;
    const PRODUCT_WORDTRIBE = 3;



    ##### 学科 ##########
    const SUBJECT_MATH          = 0;
    const SUBJECT_CHINESE       = 1;
    const SUBJECT_ENGLISH       = 2;
    const SUBJECT_PHYSICS       = 3;
    const SUBJECT_CHEMISTRY     = 4;
    const SUBJECT_BIOLOGY       = 5;
    const SUBJECT_HISTORY       = 6;
    const SUBJECT_GEOGRAPHY     = 7;
    const SUBJECT_POLITICS      = 8;
    const SUBJECT_INFORMATION   = 9;

    const ENUM_SUBJECT = [
        self::SUBJECT_MATH          => '数学',
        self::SUBJECT_CHINESE       => '语文',
        self::SUBJECT_ENGLISH       => '英语',
        self::SUBJECT_PHYSICS       => '物理',
        self::SUBJECT_CHEMISTRY     => '化学',
        self::SUBJECT_BIOLOGY       => '生物',
        self::SUBJECT_HISTORY       => '历史',
        self::SUBJECT_GEOGRAPHY     => '地理',
        self::SUBJECT_POLITICS      => '政治',
        self::SUBJECT_INFORMATION   => '信息技术'
    ];

    ##### 学段 ##########
    const GRADEPART_GRADE = 10;
    const GRADEPART_MIDDLE = 20;
    const GRADEPART_HIGH = 30;

    const ENUM_GRADEPART = [
        self::GRADEPART_GRADE =>  '小学',
        self::GRADEPART_MIDDLE=>  '初中',
        self::GRADEPART_HIGH  =>  '高中'
    ];

    const ENUM_GRADEPART_GRADE = [
        self::GRADEPART_GRADE =>  '小学'
    ];

    const ENUM_GRADEPART_KBOX = [
        self::GRADEPART_GRADE => '小学',
        self::GRADEPART_MIDDLE=>  '初中',
        self::GRADEPART_HIGH  =>  '高中'
    ];

    const ENUM_GRADE = [
        1 => '小学一年级',
        2 => '小学二年级',
        3 => '小学三年级',
        4 => '小学四年级',
        5 => '小学五年级',
        6 => '小学六年级',
        11 => '初中六年级',
        12 => '初中七年级',
        13 => '初中八年级',
        14 => '初中九年级',
        21 => '高中一年级',
        22 => '高中二年级',
        23 => '高中三年级',
    ];

    const ENUM_GRADE_ALL = [
        10=>'小学',
        20=>'初中',
        30=>'高中',
        1 => '小学一年级',
        2 => '小学二年级',
        3 => '小学三年级',
        4 => '小学四年级',
        5 => '小学五年级',
        6 => '小学六年级',
        11 => '初中六年级',
        12 => '初中七年级',
        13 => '初中八年级',
        14 => '初中九年级',
        21 => '高中一年级',
        22 => '高中二年级',
        23 => '高中三年级',
    ];

    const ENUM_PAPER_PAPERTYPE = [
        1   => '期末试卷',
        2   => '期中试卷',
        3   => '月考试卷',
        4   => '单元测试',
        5   => '原创试卷',
        6   => '竞赛测试',
        7   => '高考真题',
        8   => '中考真题',
        9   => '水平会考',
        10   => '专题试卷',
        11   => '开学考试',
        12   => '高考联考模拟',
        13   => '高考名校模拟',
        14   => '调研联考',
        15   => '中考联考模拟',
        16   => '中考名校模拟',
        17   => '小升初',
    ];


    ############## 报错类型 ##########
    const ERROR_TYPE = [
        -1 => '全部',
        0 => '题干有误（旧）',
        1 => '答案有误（旧）',
        2 => '解析有误（旧）',
        3 => '题型有误（旧）',
        4 => '章节结构有误（旧）',
        5 => '知识点有误',
        6 => '其他错误',
        11 => '答案有误',
        12 => '解析错误',
        13 => '解析中的笔误',
        14 => '排版错误',
        15 => '错别字、标点符号、多字缺字及其他非知识性错误',
        16 => '题干错误',
        17 => '选项有误、选项重复',
        //susuan
        100=>'未知错误',
        101=>'答案错误',
        102=>'题干错误',
        103=>'解析错误',
        104=>'知识点不符',
        105=>'内容超纲',
        106=>'其它'
    ];
    const ERROR_TYPE_SUSUAN_ONLINE = [
        1=>'答案错误',
        2=>'题干错误',
        3=>'解析错误',
        4=>'知识点不符',
        5=>'内容超纲',
        6=>'其它'
    ];

    const ERROR_CHECK_STATE = [
        '-1' => '全部',
        '0' => '未报错',
        '1' => '一校合格',
        '2' => '二校合格',
        '10' => '一校报错',
        '20' => '二校报错',
        '11' => '一校修改后通过（报错正确）',
        '14' => '一校修改后通过（报错分类错误）',
        '12' => '一校误报',
        '13' => '一校废弃（报错正确）',
        '15' => '一校废弃（报错分类错误）',
        '21' => '二校修改后通过（报错正确）',
        '24' => '二校修改后通过（报错分类错误）',
        '22' => '二校误报',
        '23' => '二校废弃（报错正确）',
        '25' => '二校废弃（报错分类错误）',
    ];
    const ERROR_CHECK_STATE_RI_CHANG = [
        '-2'=>'全部',
        '100'=>'未处理',
        '101'=>'修改后通过',
        '102'=>'误报',
        '103'=>'废弃',
    ];
    const ERROR_CHECK_STATE_SELECT = [
        '1'=>'修改后通过（报错正确）',
        '4'=>'修改后通过（报错分类错误）',
        '2'=>'误报',
        '3'=>'废弃（报错正确）',
        '5'=>'废弃（报错分类错误）',
    ];

    const ERROR_CHECK_STATE_SELECT_RICHANG = [
        '1'=>'修改后通过',
        '2'=>'误报',
        '3'=>'废弃',
    ];
//    const QUESTION_TYPE = [
//        0=>'单选题',
//        1=>'多选题',
//        2=>'解答题',
////        3=>'填空',
////        4=>'翻译',
//        5=>'完形',
//        6=>'阅读理解',
////        7=>'语法',
//    ];


    ######## 上线状态 ############
    const ONLINE_STATUS = [
        0 => '未上线',
        1 => '待上线',
        2 => '已上线',
        3 => '待下线',
    ];



    ####### 题型 ####################
    const SYSTEM_TYPE = [
        0 => '单选',
        1 => '多选',
        2 => '填空',
        3 => '解答',
        5 => '复合',
    ];
    const SHOW_TYPE = [
        0 => '选择题',
        1 => '多选题',
        2 => '解答题',
        3 => '填空',
        4 => '翻译',
        5 => '完形',
        6 => '阅读理解',
        7 => '听力',
        11 => '短对话',
        12 => '长对话',
        13 => '独白',
    ];

    const SHOW_TYPE_PRODUCT = [
        self::PRODUCT_KBOX => [
            0 => '选择题',
            1 => '多选题',
            2 => '解答题',
//            3 => '填空',
//            4 => '翻译',
            5 => '完形',
            6 => '阅读理解',
//            7 => '听力',
            11 => '短对话',
            12 => '长对话',
            13 => '独白',
        ],
        self::PRODUCT_SUSUAN => [
            0 => '选择题',
            3 => '填空题',
            20=>'填空题new',
            21=>'选择题new',
            22=>'选词填空',
            23=>'连词成句',
            24=>'朗读题',
            25=>'判断题',
            26=>'背诵题',//无答案 无选项
            //27=>'朗读题',
            28=>'图片挖空',
        ],
        self::PRODUCT_WORDTRIBE => [
            0=>'选择题'
        ]
    ];


    const TYPE_SYS_CHOICE = 0;
    const TYPE_SYS_MUTICHOICE = 1;
    const TYPE_SYS_FILLIN = 2;
    const TYPE_SYS_ANSWER = 3;
    const TYPE_SYS_PARENT = 5;

    const TYPE_SHOW_DANXUAN = 0;
    const TYPE_SHOW_DUOXUAN = 1;
    const TYPE_SHOW_JIEDA = 2;
    const TYPE_SHOW_TIANKONG = 3;
    const TYPE_SHOW_WANXING = 5;
    const TYPE_SHOW_YUEDU = 6;
    const TYPE_SHOW_DUANDUIHUA = 11;
    const TYPE_SHOW_CHANGDUIHUA = 12;
    const TYPE_SHOW_DUBAI = 13;

    const ENUM_TYPE_CHOICE_LIST = [self::TYPE_SHOW_DANXUAN, self::TYPE_SHOW_DUOXUAN, self::TYPE_SHOW_DUANDUIHUA];
    const ENUM_TYPE_ANSWER_LIST = [self::TYPE_SHOW_JIEDA,self::TYPE_SHOW_TIANKONG];
    const ENUM_TYPE_PARENT_LIST = [self::TYPE_SHOW_WANXING, self::TYPE_SHOW_YUEDU, self::TYPE_SHOW_CHANGDUIHUA, self::TYPE_SHOW_DUBAI];
    const ENUM_TYPE_SHOW_SYS = [
        self::TYPE_SHOW_DANXUAN => self::TYPE_SYS_CHOICE,
        self::TYPE_SHOW_DUOXUAN => self::TYPE_SYS_MUTICHOICE,
        self::TYPE_SHOW_JIEDA => self::TYPE_SYS_ANSWER,
        self::TYPE_SHOW_TIANKONG => self::TYPE_SYS_FILLIN,
        self::TYPE_SHOW_WANXING => self::TYPE_SYS_PARENT,
        self::TYPE_SHOW_YUEDU => self::TYPE_SYS_PARENT,
        self::TYPE_SHOW_DUANDUIHUA => self::TYPE_SYS_CHOICE,
        self::TYPE_SHOW_CHANGDUIHUA => self::TYPE_SYS_PARENT,
        self::TYPE_SHOW_DUBAI => self::TYPE_SYS_PARENT,
        20=>20,//'填空题',
        21=>21,//'选择题',
        22=>22,//'选词填空',
        23=>23,//'连词成句',
        24=>24,//'朗读题',
        25=>25,//'判断题',
        26=>26,//'背诵题',
        28=>28,//图片挖空
    ];









    ##### 难度 ###############
    const QUESTION_DIFFICULT = [
        4 => '难度A',
        1 => '难度B',
        2 => '难度C',
        3 => '难度D',
        0 => '未知',
    ];


    const SUMMARY_NOT = "NOT_ALLOW_ONLINE";//禁止上线


    ##### 选精 ###############
    const CHOICE_TYPE_BAD = 99;
    const ENUM_CHOICE_TYPE = [
        0=>'普通题',
        1=>'课后题',
        2=>'扩展题',
        self::CHOICE_TYPE_BAD =>'坏题',
    ];



}
