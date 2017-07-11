CREATE TABLE `bookstore_auth_relate_user_role` (
  `relate_user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_user_role_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_auth_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_auth_user` (
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_assist` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `assist_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL DEFAULT '0',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `assist_id` (`assist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_auth_user` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `op_type` int(11) NOT NULL,
  `memo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '录入  打回 修改/二次录入 审核通过 删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_book` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `book_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_edition` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `edition_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `edition_id` (`edition_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_paper` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL,
  `memo` text NOT NULL,
  `paper_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `paper_id` (`paper_id`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_section` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `section_id` (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

CREATE TABLE `common_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `module` varchar(20) NOT NULL DEFAULT '',
  `role` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

CREATE TABLE `common_modules_user` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `module` varchar(20) NOT NULL DEFAULT '',
  `role` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_auth_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '1 录题 2校验',
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`),
  KEY `admin_id` (`admin_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10700 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_auth_relate_user_role` (
  `relate_user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_user_role_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6497 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_auth_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_auth_user` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `task_count` int(11) NOT NULL DEFAULT '0',
  `cur_task_count` int(11) NOT NULL DEFAULT '0',
  `question_count` int(11) NOT NULL DEFAULT '0' COMMENT '题目数量',
  `question_onepass_count` int(11) NOT NULL DEFAULT '0' COMMENT '一次通过的题目数量',
  `free_state` int(11) NOT NULL DEFAULT '0',
  `pass_rate` float(11,0) NOT NULL DEFAULT '0' COMMENT '一次通过率',
  `point` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_log_auth_user` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `free_status` int(11) NOT NULL,
  `op_type` int(11) NOT NULL,
  `memo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '录入  打回 修改/二次录入 审核通过 删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=843 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_log_task` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `op_type` int(11) NOT NULL COMMENT '操作类型 1添加  2删除 3修改',
  `op_type_sub` int(11) NOT NULL DEFAULT '-1' COMMENT '11 录题提交 12 返工录题提交  13 校验通过 14 校验返工  -1 无',
  `memo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '录入  打回 修改/二次录入 审核通过 删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `task_id` (`task_id`,`user_id`,`op_type`,`op_type_sub`),
  KEY `user_id` (`user_id`,`op_type`,`op_type_sub`)
) ENGINE=InnoDB AUTO_INCREMENT=739 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_log_taskquestion` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_search` int(11) NOT NULL DEFAULT '0',
  `mark` text NOT NULL COMMENT '备注信息',
  `stage` int(11) NOT NULL DEFAULT '0' COMMENT '0未审核 1审核通过 2返工 99删除',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_question_entity` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` int(11) NOT NULL DEFAULT '-1' COMMENT '学科',
  `gradepart` int(11) NOT NULL COMMENT '学段',
  `type` int(11) NOT NULL COMMENT '题型',
  `type_show` int(11) NOT NULL COMMENT '展示题型',
  `option_count` int(11) NOT NULL DEFAULT '0' COMMENT '选项个数',
  `difficulty` int(11) NOT NULL DEFAULT '0' COMMENT '难度',
  `level` int(11) NOT NULL DEFAULT '1' COMMENT '层级',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级题目id',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '题号',
  `stem` text NOT NULL COMMENT '提干',
  `stem_origin` text NOT NULL COMMENT '原始提干',
  `answer` text NOT NULL COMMENT '答案',
  `answer_origin` text NOT NULL COMMENT '原始答案',
  `explain` text NOT NULL COMMENT '答案解析',
  `explain_origin` text NOT NULL COMMENT '原始答案解析',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`),
  KEY `parent_id` (`parent_id`),
  KEY `subject` (`subject`,`gradepart`)
) ENGINE=InnoDB AUTO_INCREMENT=16883846 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_question_item` (
  `question_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `code` char(1) NOT NULL COMMENT '选项编码',
  `item` text NOT NULL COMMENT '选项',
  `item_origin` text NOT NULL,
  `is_right` tinyint(4) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_item_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2316628 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_task_entity` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '任务名',
  `name_ext` varchar(255) NOT NULL DEFAULT '',
  `resource_type` int(11) NOT NULL COMMENT '题目来源类型 1电子 2纸质',
  `resource_url` varchar(200) NOT NULL DEFAULT '' COMMENT '资源地址',
  `subject_type` int(11) NOT NULL COMMENT '1文 2理',
  `subject` int(11) NOT NULL DEFAULT '-1',
  `gradepart` int(11) NOT NULL,
  `relate_type` int(11) NOT NULL COMMENT '录入位置 1章节 2试卷 ',
  `relate_id` int(11) NOT NULL COMMENT '章节id或试卷id  根据type而定',
  `source_type` int(11) NOT NULL COMMENT '录入目标来源 0未设定 1章节 2试卷 11教辅',
  `source_id` int(11) NOT NULL COMMENT '来源位置  id',
  `mark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注 状态变更后的返工原因等',
  `is_pay` int(11) NOT NULL DEFAULT '0' COMMENT '0未支付 1已支付',
  `question_count` int(11) NOT NULL DEFAULT '0' COMMENT '总题目数量',
  `question_choice_count` int(11) NOT NULL DEFAULT '0' COMMENT '选择题数量',
  `question_answer_count` int(11) NOT NULL DEFAULT '0' COMMENT '解答题数量',
  `question_sub_count` int(11) NOT NULL DEFAULT '0' COMMENT '小题数量',
  `question_pass_count` int(11) NOT NULL DEFAULT '0' COMMENT '合格题目数量',
  `question_rework_count` int(11) NOT NULL DEFAULT '0' COMMENT '返工数量',
  `question_rework_count_all` int(11) NOT NULL DEFAULT '0' COMMENT '总返工次数',
  `entry_group` int(11) NOT NULL DEFAULT '0',
  `entry_user` int(11) NOT NULL DEFAULT '0',
  `check_user` int(11) NOT NULL DEFAULT '0',
  `entry_submit_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '录入第一次提交时间',
  `entry_finish_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '录入完成时间',
  `entry_dead_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entry_overtime_day` int(11) NOT NULL DEFAULT '0' COMMENT '录入超时天数',
  `check_submit_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核第一次完成时间',
  `check_finish_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核完成时间',
  `check_dead_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '校验截止时间',
  `check_overtime_day` int(11) NOT NULL DEFAULT '0' COMMENT '校验超时天数',
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '支付时间',
  `stage` int(11) NOT NULL DEFAULT '0' COMMENT '阶段 0未分配 11已分配到组 12已分配到人未录入 21已录入未验收 30已验收  40返工',
  `point` int(11) NOT NULL DEFAULT '0' COMMENT '任务积分',
  `online_status` int(11) NOT NULL DEFAULT '0' COMMENT '上线状态 0未上线 1上线成功 2上线失败 3上线中',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除',
  `create_user` int(11) NOT NULL DEFAULT '0' COMMENT '创建用户',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  KEY `subject` (`subject`,`gradepart`),
  KEY `relate_id` (`relate_id`,`relate_type`),
  KEY `source_id` (`source_id`,`source_type`),
  KEY `stage` (`stage`),
  KEY `entry_group` (`entry_group`,`stage`),
  KEY `entry_user` (`entry_user`,`stage`),
  KEY `check_user` (`check_user`,`stage`)
) ENGINE=InnoDB AUTO_INCREMENT=31837 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_task_relate_question` (
  `relate_task_question_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_type` int(11) NOT NULL DEFAULT '-1' COMMENT '题型',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `number` int(11) NOT NULL,
  `is_search` int(11) NOT NULL DEFAULT '0',
  `mark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注 返工原因',
  `point` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `stage` int(11) NOT NULL DEFAULT '0' COMMENT '0未审核 1审核通过 2返工',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `rework_count` int(11) NOT NULL DEFAULT '0' COMMENT '返工次数',
  `sub_count` int(11) NOT NULL DEFAULT '0' COMMENT '子题目数量',
  PRIMARY KEY (`relate_task_question_id`),
  KEY `question_id` (`question_id`,`is_search`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1700494 DEFAULT CHARSET=utf8;

CREATE TABLE `entrystore_word_list` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL DEFAULT '',
  `gqs` varchar(255) NOT NULL DEFAULT '',
  `gqfc` varchar(255) NOT NULL DEFAULT '',
  `xzfc` varchar(255) NOT NULL DEFAULT '',
  `fs` varchar(255) NOT NULL DEFAULT '',
  `meaning` varchar(255) NOT NULL DEFAULT '',
  `lx` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_auth_relate_user_role` (
  `relate_user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_user_role_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_auth_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_auth_user` (
  `user_id` int(11) NOT NULL,
  `grade_part` int(11) NOT NULL DEFAULT '0',
  `subject` int(11) NOT NULL DEFAULT '0' COMMENT '学科',
  `check_count_round1` int(11) NOT NULL DEFAULT '0' COMMENT '校验数量',
  `check_count_round2` int(11) NOT NULL DEFAULT '0' COMMENT '校验数量',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `subject` (`subject`,`grade_part`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_check_daily_syn` (
  `error_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_check_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round` int(11) NOT NULL COMMENT '轮数 10日常 其他外包轮数',
  `question_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_ok` int(11) NOT NULL,
  `error_status` int(10) unsigned NOT NULL DEFAULT '0',
  `error_type` int(10) unsigned NOT NULL DEFAULT '0',
  `error_info` longtext NOT NULL,
  `check_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id_online` int(10) unsigned NOT NULL DEFAULT '0',
  `is_teacher_online` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `QuestionID` (`question_id`),
  KEY `ErrorType` (`error_type`),
  KEY `TeacherID` (`user_id`),
  KEY `State` (`error_status`,`check_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1361965 DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_check_questions` (
  `question_id` int(10) unsigned NOT NULL,
  `subject` int(11) NOT NULL,
  `grade_part` int(10) unsigned NOT NULL,
  `question_type` int(11) NOT NULL,
  `directory_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'last TeacherID',
  `is_admin` int(11) NOT NULL DEFAULT '0',
  `round` int(11) NOT NULL DEFAULT '1' COMMENT '轮数',
  `error_status` int(11) NOT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`),
  KEY `KnowledgeID` (`directory_id`),
  KEY `IsFinal` (`is_admin`),
  KEY `AddTime` (`create_time`),
  KEY `Subject` (`subject`,`grade_part`) USING BTREE,
  KEY `Status` (`error_status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `qualitysys_log_auth_user` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `op_type` int(11) NOT NULL,
  `memo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '录入  打回 修改/二次录入 审核通过 删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8;

CREATE TABLE `qualitysys_log_question` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0question 1item',
  `user_id` int(11) NOT NULL,
  `mark` text NOT NULL,
  `syn_status` int(11) NOT NULL DEFAULT '0' COMMENT '0默认 1正在同步 2同步成功 3同步失败',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `question_id` (`question_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1349729 DEFAULT CHARSET=utf8mb4;
