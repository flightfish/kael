
CREATE TABLE `global_setting` (
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_issue` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `issue_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bookstore_log_know` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `op_type` int(11) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `know_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `know_id` (`know_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `entrystore_question_entity`
add `render` text NOT NULL,
add `core` text not null;

ALTER TABLE `qualitysys_log_question`
add `resource_status` int(11) NOT NULL;


CREATE TABLE `qselect_auth_relate_user_role` (
  `relate_user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`relate_user_role_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_auth_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_auth_user` (
  `user_id` int(11) NOT NULL,
  `grade_part` int(11) NOT NULL DEFAULT '0',
  `subject` int(11) NOT NULL DEFAULT '0' COMMENT '学科',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `subject` (`subject`,`grade_part`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qselect_bad_type` (
  `bad_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bad_type_id`),
  KEY `task_id` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_log_select` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL COMMENT '新增/删除的标签id',
  `question_id` int(11) NOT NULL DEFAULT '0',
  `select_id` int(11) NOT NULL COMMENT '0 无select 非任务流   >0任务流',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `stage` int(11) NOT NULL DEFAULT '0' COMMENT '状态 -1非任务  0-xx当时题目状态(选精、审核、修改后通过、通过）',
  `mark` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `select_id` (`select_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_log_tag_online` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `relate_id` int(11) NOT NULL COMMENT '新增/删除的标签id',
  `relate_type` int(11) NOT NULL COMMENT '新增/删除的标签type',
  `op_type` int(11) NOT NULL DEFAULT '0' COMMENT '0未知 1主 2副 3删',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_log_task` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `op_type` int(11) NOT NULL COMMENT '操作类型 1添加  2删除 3修改 4分配选精员',
  `op_ext` varchar(255) NOT NULL DEFAULT '',
  `memo` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '录入  打回 修改/二次录入 审核通过 删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `task_id` (`task_id`,`user_id`,`op_type`),
  KEY `user_id` (`user_id`,`op_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_select_entity` (
  `select_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT '0',
  `sub_relate_id` int(11) NOT NULL DEFAULT '0',
  `question_id` int(11) NOT NULL DEFAULT '0' COMMENT '任务名',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `subject` int(11) NOT NULL DEFAULT '-1',
  `grade_part` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `stage` int(11) NOT NULL DEFAULT '0' COMMENT '阶段 0未分配 1选精阶段 2审核阶段  3通过  4不通过 5坏题',
  `check_id` int(11) NOT NULL DEFAULT '0' COMMENT '检查人id',
  `error_status` int(11) NOT NULL DEFAULT '0' COMMENT '0好题 其他 error状态枚举',
  `error_info` varchar(100) NOT NULL DEFAULT '' COMMENT '错误原因',
  `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '返工原因',
  `online_status` int(11) NOT NULL DEFAULT '0' COMMENT '上线状态 0未上线 1上线成功 2上线失败 3上线中',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除',
  `create_step` varchar(100) NOT NULL DEFAULT 'select',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`select_id`),
  KEY `subject` (`subject`,`grade_part`),
  KEY `stage` (`stage`),
  KEY `entry_group` (`stage`),
  KEY `entry_user` (`stage`),
  KEY `check_user` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_select_tag` (
  `select_tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `select_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL DEFAULT '0' COMMENT '任务名',
  `relate_type` int(11) NOT NULL DEFAULT '0',
  `relate_id` int(11) NOT NULL DEFAULT '-1',
  `main` tinyint(4) NOT NULL DEFAULT '0',
  `difficulty` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`select_tag_id`),
  UNIQUE KEY `select_id` (`select_id`,`question_id`,`relate_type`,`relate_id`) USING BTREE,
  KEY `relate_type` (`relate_type`,`relate_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `qselect_task_entity` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '任务名',
  `name_ext` varchar(255) NOT NULL DEFAULT '',
  `subject` int(11) NOT NULL DEFAULT '-1',
  `grade_part` int(11) NOT NULL,
  `susuan_type` int(11) not null default -1 comment '0口算 1基础 -1默认',
  `relate_type` int(11) NOT NULL COMMENT '录入位置 1章节 2试卷  11教辅',
  `relate_id` int(11) NOT NULL COMMENT '章节id或试卷id  根据type而定',
  `question_start_time` timestamp not null default '0000-00-00 00:00:00' comment '题目最早创建时间',
  `question_end_time` timestamp not null default '2038-01-01' comment '题目最晚创建时间',
  `mark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注 状态变更后的返工原因等',
  `select_user` int(11) NOT NULL DEFAULT '0',
  `check_user` int(11) NOT NULL DEFAULT '0',
  `question_count` int(11) NOT NULL DEFAULT '0',
  `online_status` int(11) NOT NULL DEFAULT '0' COMMENT '上线状态 0未上线 1上线成功 2上线失败 3上线中',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0正常 1删除 2已结束',
  `create_user` int(11) NOT NULL DEFAULT '0' COMMENT '创建用户',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`task_id`),
  KEY `subject` (`subject`,`grade_part`),
  KEY `relate_id` (`relate_id`,`relate_type`),
  KEY `entry_user` (`select_user`),
  KEY `check_user` (`check_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


REPLACE INTO bookstore_auth_role (`role_id`,`name`) VALUES
  (1,'超级管理员'),
  (2,'试卷管理'),
  (3,'教辅管理'),
  (4,'知识点管理');

REPLACE INTO entrystore_auth_role (`role_id`,`name`) VALUES
  (1,'校验员'),
  (2,'录入员'),
  (3,'组管理员'),
  (4,'超级管理员');

REPLACE INTO qselect_auth_role (`role_id`,`name`) VALUES
  (1,'超级管理员'),
  (2,'选精'),
  (3,'审核');

REPLACE INTO qualitysys_auth_role (`role_id`,`name`) VALUES
  (1,'超级管理员'),
  (2,'一校员'),
  (4,'二校员'),
  (3,'错误管理');





alter table qualitysys_check_log  add source_id int not null default 0 comment '首次报错logid 0首次操作';



CREATE TABLE `entrystore_question_jsonstyletype` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` int(11) NOT NULL DEFAULT '-1',
  `grade_part` int(11) NOT NULL DEFAULT '-1',
  `type_name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `type_style` varchar(100) NOT NULL DEFAULT '' COMMENT '样式',
  `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` int(11) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `entrystore_question_jsonstyletype` VALUES ('1', '1', '10', '引导语', 'chinese_guide', '', '0', '2017-05-15 12:33:13', '2017-05-15 12:33:13');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('2', '1', '10', '题干文字', 'chinese_text', '文字＋小图时，用该样式', '0', '2017-05-15 12:33:13', '2017-05-15 12:33:13');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('3', '1', '10', '题干图片', 'chinese_picture', '大图标签 自带尺寸', '0', '2017-05-15 12:33:13', '2017-05-15 12:33:13');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('4', '1', '10', '题干音频', 'chinese_audio', '', '0', '2017-05-15 12:33:13', '2017-05-15 12:33:13');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('5', '1', '10', '题干提示文字', 'chinese_tiptext', '', '0', '2017-05-15 12:33:13', '2017-05-15 12:33:13');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('6', '2', '10', '引导语', 'english_guide', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('7', '2', '10', '题干文字', 'english_text', '文字＋小图时，用该样式', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('8', '2', '10', '题干图片', 'english_picture', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('9', '2', '10', '题干音频', 'english_audio', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('10', '2', '10', '题干提示文字', 'english_tiptext', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('11', '2', '10', '单词挖空', 'english_blank', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('12', '2', '10', '单词全拼', 'english_spell', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('13', '2', '10', '连词成句输入区', 'english_sentence', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('14', '2', '10', '朗读题题干', 'english_read', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('15', '2', '10', '背诵题题干', 'english_recite', '', '0', '2017-05-15 12:33:30', '2017-05-15 12:33:33');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('16', '0', '10', '引导语', 'math_guide', '', '0', '2017-05-15 12:33:48', '2017-05-15 12:33:52');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('17', '0', '10', '题干文字', 'math_text', '文字＋小图时，用该样式', '0', '2017-05-15 12:33:48', '2017-05-15 12:33:52');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('18', '0', '10', '题干图片', 'math_picture', '大图标签 自带尺寸', '0', '2017-05-15 12:33:48', '2017-05-15 12:33:52');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('19', '0', '10', '题干音频', 'math_audio', '', '0', '2017-05-15 12:33:48', '2017-05-15 12:33:52');
INSERT INTO `entrystore_question_jsonstyletype` VALUES ('20', '0', '10', '题干提示文字', 'math_tiptext', '', '0', '2017-05-15 12:33:48', '2017-05-15 12:33:52');


